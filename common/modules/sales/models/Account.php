<?php

namespace common\modules\sales\models;

use Yii;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\components\behaviors\TokenProtectedFormBehavior;
use common\components\behaviors\LoggableBehavior;

use common\modules\master\models\StatusActive;
use common\modules\master\models\Country;
use common\modules\master\models\Province;
use common\modules\master\models\City;
use common\modules\master\models\PostalCode;
use common\modules\master\models\Team;

use common\modules\auth\models\User;

use common\modules\productprice\models\PriceList;
use common\modules\productprice\models\Product;

class Account extends ActiveRecord
{

    /**
     * ENUM field values
     */
    const ACCOUNT_TYPE_PROSPECT = 'Prospect';
    const ACCOUNT_TYPE_CUSTOMER = 'Customer';
    const ACCOUNT_TYPE_PARTNER = 'Partner';
    const ACCOUNT_TYPE_RESELLER = 'Reseller';
    const ACCOUNT_TYPE_VENDOR = 'Vendor';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'account';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        if ($this instanceof UserSearch) {
            return [];
        }

        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'tokenProtection' => [
                'class' => TokenProtectedFormBehavior::class,
                'tokenAttribute' => 'form_token',
                'sessionKey' => 'account_token',
            ],
            [
                'class' => LoggableBehavior::class,
                'modelName' => 'Account',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_account_id', 'code', 'industry', 'tax_number', 'phone', 'email', 'website', 'address', 'price_list_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'owner_user_id', 'assigned_user_id', 'customer_segment'], 'default', 'value' => null],
            [['account_type'], 'default', 'value' => 'Prospect'],
            [['status_id'], 'default', 'value' => 1],
            [['parent_account_id', 'price_list_id', 'city_id', 'province_id', 'country_id', 'postal_code_id', 'status_id', 'created_by', 'updated_by', 'owner_user_id', 'assigned_user_id'], 'integer'],
            [['name'], 'required'],
            [['account_type', 'address'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['code', 'phone', 'customer_segment'], 'string', 'max' => 50],
            [['name', 'description'], 'string', 'max' => 255],
            [['industry', 'tax_number', 'email'], 'string', 'max' => 100],
            [['website'], 'string', 'max' => 150],
            ['account_type', 'in', 'range' => array_keys(self::optsAccountType())],
            [['code'], 'unique'],
            // owner_user_id holds the sales TEAM (FK team.id), despite the name
            [['owner_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Team::class, 'targetAttribute' => ['owner_user_id' => 'id']],
            [['assigned_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['assigned_user_id' => 'id']],
            [['parent_account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['parent_account_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => StatusActive::class, 'targetAttribute' => ['status_id' => 'id']],
            [['postal_code_id'], function () {
                \common\components\LocationRules::check($this);
            }, 'skipOnEmpty' => false],
            [['email'], 'email'],
            // Assigned Sales must belong to the account's Sales Team. Checked only when
            // either changes, so older assignments don't block unrelated edits.
            [['assigned_user_id'], function ($attribute) {
                if (!$this->assigned_user_id) {
                    return;
                }
                $changed = $this->isNewRecord
                    || $this->isAttributeChanged('assigned_user_id', false)
                    || $this->isAttributeChanged('owner_user_id', false);
                if (!$changed) {
                    return;
                }
                if (!$this->owner_user_id) {
                    $this->addError($attribute, 'Choose a Sales Team before assigning a salesperson.');
                } elseif (!User::find()->where(['id' => $this->assigned_user_id, 'team_id' => $this->owner_user_id])->exists()) {
                    $this->addError($attribute, 'Assigned Sales must be a member of the selected Sales Team.');
                }
            }, 'skipOnEmpty' => false],
            [['owner_user_id', 'assigned_user_id'], function ($attribute) {
                \common\components\rbac\SalesAccess::checkAssignment($this, $attribute);
            }, 'skipOnEmpty' => false],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::class, 'targetAttribute' => ['country_id' => 'id']],
            [['province_id'], 'exist', 'skipOnError' => true, 'targetClass' => Province::class, 'targetAttribute' => ['province_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id']],
            [['postal_code_id'], 'exist', 'skipOnError' => true, 'targetClass' => PostalCode::class, 'targetAttribute' => ['postal_code_id' => 'id']],
            [['price_list_id'], 'exist', 'skipOnError' => true, 'targetClass' => PriceList::class, 'targetAttribute' => ['price_list_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_account_id' => 'Group Account',
            'code' => 'Code',
            'name' => 'Name',
            'account_type' => 'Customer Type',
            'customer_segment' => 'Customer Segment',
            'industry' => 'Industry',
            'tax_number' => 'Tax Number',
            'phone' => 'Phone',
            'email' => 'Email',
            'website' => 'Website',
            'address' => 'Address',
            'city_id' => 'City',
            'province_id' => 'Province',
            'country_id' => 'Country',
            'postal_code_id' => 'Postal Code',
            'price_list_id' => 'Price List',
            'description' => 'Description',
            'status_id' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'owner_user_id' => 'Sales Team',
            'assigned_user_id' => 'Assigned Sales',
        ];
    }

    public function getAccounts()
    {
        return $this->hasMany(Account::class, ['parent_account_id' => 'id']);
    }

    public function getCountry()
    {
        return $this->hasOne(Country::class, ['id' => 'country_id']);
    }

    public function getProvince()
    {
        return $this->hasOne(Province::class, ['id' => 'province_id']);
    }

    public function getCity()
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    public function getPostalCode()
    {
        return $this->hasOne(PostalCode::class, ['id' => 'postal_code_id']);
    }

    public function getActivities()
    {
        return $this->hasMany(Activity::class, ['account_id' => 'id']);
    }

    public function getContacts()
    {
        return $this->hasMany(Contact::class, ['account_id' => 'id']);
    }

    public function getInvoices()
    {
        return $this->hasMany(Invoice::class, ['account_id' => 'id']);
    }

    public function getOpportunities()
    {
        return $this->hasMany(Opportunity::class, ['account_id' => 'id']);
    }

    public function getTeam()
    {
        return $this->hasOne(Team::class, ['id' => 'owner_user_id']);
    }

    /**
     * Opportunities of this account that were never worked on, like the one
     * Convert creates: still Prospecting, and no products, quotations or
     * activities. These go with the account; any other opportunity blocks it.
     *
     * @return Opportunity[]
     */
    public function untouchedOpportunities()
    {
        return Opportunity::find()->alias('o')
            ->where(['o.account_id' => $this->id, 'o.stage' => 'Prospecting'])
            ->andWhere(['not exists', OpportunityProduct::find()->where('opportunity_id = o.id')])
            ->andWhere(['not exists', Quotation::find()->where('opportunity_id = o.id')])
            ->andWhere(['not exists', Activity::find()->where('opportunity_id = o.id')])
            ->all();
    }

    /**
     * Why this account can't be deleted, or null if it can. Opportunities in
     * progress, quotations, sales orders, invoices and activities (also those
     * pointing at one of its contacts) must be deleted or moved first; untouched
     * opportunities, contacts, addresses and documents go with the account (see
     * deleteWithDependents()).
     */
    public function deleteBlockers()
    {
        $contactIds = Contact::find()->select('id')->where(['account_id' => $this->id])->column();
        $untouchedIds = array_map(fn($o) => $o->id, $this->untouchedOpportunities());

        $parts = [];

        $inProgress = Opportunity::find()->where(['account_id' => $this->id])
            ->orFilterWhere(['contact_id' => $contactIds ?: null])
            ->andFilterWhere(['not in', 'id', $untouchedIds ?: null])
            ->orderBy(['id' => SORT_ASC])
            ->all();
        if (count($inProgress) === 1) {
            $o = $inProgress[0];
            $kind = in_array($o->stage, ['Closed Won', 'Closed Lost'], true) ? 'closed opportunity' : 'opportunity in progress';
            $parts[] = sprintf('1 %s ("%s", stage %s, Rp %s)',
                $kind, $o->name, $o->stage, number_format((float) $o->amount, 0, ',', '.'));
        } elseif ($inProgress) {
            $parts[] = count($inProgress) . ' opportunities that are in progress or closed';
        }

        $counts = [
            'quotation'   => (int) Quotation::find()->where(['account_id' => $this->id])->count(),
            'sales order' => (int) SalesOrder::find()->where(['account_id' => $this->id])->count(),
            'invoice'     => (int) Invoice::find()->where(['account_id' => $this->id])->count(),
            'activity'    => (int) Activity::find()->where(['account_id' => $this->id])
                ->orFilterWhere(['contact_id' => $contactIds ?: null])->count(),
        ];
        foreach ($counts as $label => $n) {
            if ($n > 0) {
                $plural = $label === 'activity' ? 'activities' : $label . 's';
                $parts[] = $n . ' ' . ($n === 1 ? $label : $plural);
            }
        }
        if (!$parts) {
            return null;
        }

        $last = array_pop($parts);
        $list = $parts ? implode(', ', $parts) . ' and ' . $last : $last;
        $it = (count($parts) === 0 && (count($inProgress) === 1 || array_sum($counts) === 1)) ? 'it' : 'them';
        return "{$this->name} still has {$list}. Delete or move {$it} first.";
    }

    /**
     * Deletes, in one transaction and in this order, the account's untouched
     * opportunities (their stage history goes by FK cascade), contacts,
     * addresses and documents (files too), then the account itself. Records are
     * deleted through their models so each deletion lands in the audit log.
     *
     * @return array|false counts per kind ('opportunities', 'contacts', 'addresses', 'documents'), or false when blocked
     */
    public function deleteWithDependents()
    {
        if ($this->deleteBlockers() !== null) {
            return false;
        }

        $transaction = static::getDb()->beginTransaction();
        try {
            $opportunities = $this->untouchedOpportunities();
            foreach ($opportunities as $opportunity) {
                $opportunity->delete();
            }
            $contacts = Contact::find()->where(['account_id' => $this->id])->all();
            foreach ($contacts as $contact) {
                $contact->delete();
            }
            $addresses = AccountAddress::find()->where(['account_id' => $this->id])->all();
            foreach ($addresses as $address) {
                $address->delete();
            }
            $documents = AccountAttachment::find()->where(['account_id' => $this->id])->all();
            foreach ($documents as $document) {
                $document->delete();
            }
            if ($this->delete() === false) {
                throw new \RuntimeException('Account could not be deleted.');
            }
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        return [
            'opportunities' => count($opportunities),
            'contacts'      => count($contacts),
            'addresses'     => count($addresses),
            'documents'     => count($documents),
        ];
    }

    /**
     * account_attachment rows go with the account (FK ON DELETE CASCADE); remove
     * their files too.
     */
    public function afterDelete()
    {
        parent::afterDelete();
        \yii\helpers\FileHelper::removeDirectory(AccountAttachment::storageDir($this->id));
    }

    public function getAssignedUser()
    {
        return $this->hasOne(User::class, ['id' => 'assigned_user_id']);
    }

    public function getAttachments()
    {
        return $this->hasMany(AccountAttachment::class, ['account_id' => 'id']);
    }

    public function getParentAccount()
    {
        return $this->hasOne(Account::class, ['id' => 'parent_account_id']);
    }

    public function getPriceList()
    {
        return $this->hasOne(PriceList::class, ['id' => 'price_list_id']);
    }

    public function getQuotations()
    {
        return $this->hasMany(Quotation::class, ['account_id' => 'id']);
    }

    public function getSalesOrders()
    {
        return $this->hasMany(SalesOrder::class, ['account_id' => 'id']);
    }

    public function getStatus()
    {
        return $this->hasOne(StatusActive::class, ['id' => 'status_id']);
    }

    // Relasi ke user created
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    // Relasi ke user updated
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }



    /**
     * Customer segment suggestions. Same taxonomy as product.customer_type; the
     * form accepts free text too, so new segments don't need a code change.
     * @return string[]
     */
    public static function optsCustomerSegment()
    {
        return Product::optsCustomerType();
    }

    /**
     * column account_type ENUM value labels
     * @return string[]
     */
    public static function optsAccountType()
    {
        return [
            self::ACCOUNT_TYPE_PROSPECT => 'Prospect',
            self::ACCOUNT_TYPE_CUSTOMER => 'Customer',
            self::ACCOUNT_TYPE_PARTNER => 'Partner',
            self::ACCOUNT_TYPE_RESELLER => 'Reseller',
            self::ACCOUNT_TYPE_VENDOR => 'Vendor',
        ];
    }

    /**
     * @return string
     */
    public function displayAccountType()
    {
        return self::optsAccountType()[$this->account_type];
    }

    /**
     * @return bool
     */
    public function isAccountTypeProspect()
    {
        return $this->account_type === self::ACCOUNT_TYPE_PROSPECT;
    }

    public function setAccountTypeToProspect()
    {
        $this->account_type = self::ACCOUNT_TYPE_PROSPECT;
    }

    /**
     * @return bool
     */
    public function isAccountTypeCustomer()
    {
        return $this->account_type === self::ACCOUNT_TYPE_CUSTOMER;
    }

    public function setAccountTypeToCustomer()
    {
        $this->account_type = self::ACCOUNT_TYPE_CUSTOMER;
    }

    /**
     * @return bool
     */
    public function isAccountTypePartner()
    {
        return $this->account_type === self::ACCOUNT_TYPE_PARTNER;
    }

    public function setAccountTypeToPartner()
    {
        $this->account_type = self::ACCOUNT_TYPE_PARTNER;
    }

    /**
     * @return bool
     */
    public function isAccountTypeReseller()
    {
        return $this->account_type === self::ACCOUNT_TYPE_RESELLER;
    }

    public function setAccountTypeToReseller()
    {
        $this->account_type = self::ACCOUNT_TYPE_RESELLER;
    }

    /**
     * @return bool
     */
    public function isAccountTypeVendor()
    {
        return $this->account_type === self::ACCOUNT_TYPE_VENDOR;
    }

    public function setAccountTypeToVendor()
    {
        $this->account_type = self::ACCOUNT_TYPE_VENDOR;
    }

    public static function dropdown()
    {
        static $dropdown;
        if ($dropdown === null) {
            
            $models = static::find()->all();
            foreach ($models as $model) {
                $dropdown[$model->id] = $model->name;
            }
        }
            
        return $dropdown;
    }
}
