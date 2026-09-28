<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'user.passwordResetTokenExpire' => 3600,
    'user.passwordMinLength' => 8,
    'user.passwordDefault' => '12345678',
    'member.passwordResetTokenExpire' => 3600,
    'member.passwordMinLength' => 8,
    'member.passwordDefault' => '12345678',
    // Account documents; outside backend/web so files are only served through
    // AccountController (access-checked). Override in params-local.php if needed.
    'accountAttachmentPath' => '@backend/runtime/attachments/account',
    'accountAttachmentMaxSize' => 10 * 1024 * 1024,
];
