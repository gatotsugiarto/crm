<?php
namespace common\components\rbac;

use yii\base\Component;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

class Pgenerator extends Component
{
    /**
     * Ambil semua controller dan action dari path modul.
     * @param string $basePath
     * @return array
     */
    public function getControllersInModules($basePath)
    {
        $result = [];

        $modulesPath = $basePath . DIRECTORY_SEPARATOR . 'modules';
        if (!is_dir($modulesPath)) {
            return $result;
        }

        foreach (new \DirectoryIterator($modulesPath) as $module) {
            if ($module->isDot() || !$module->isDir()) continue;

            $moduleName = $module->getFilename();
            if ($moduleName === 'rights') continue;

            $controllerPath = $modulesPath . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR . 'controllers';
            $result[$moduleName]['controllers'] = $this->getControllersInPath($controllerPath);

            // recursive submodule
            $result[$moduleName]['modules'] = $this->getControllersInModules($modulesPath . DIRECTORY_SEPARATOR . $moduleName);
        }

        return $result;
    }

    /**
     * Ambil semua controller dari path utama (frontend/backend).
     * @param string $basePath
     * @return array
     */
    public function getAllControllers($basePath)
    {
        $result = [];

        if (!is_dir($basePath)) {
            return $result;
        }

        foreach (new \DirectoryIterator($basePath) as $entry) {
            if ($entry->isDot() || !$entry->isDir()) continue;

            $moduleName = $entry->getFilename();
            if ($moduleName === 'rights') continue;

            $controllerPath = $basePath . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR . 'controllers';
            $result[$moduleName]['controllers'] = $this->getControllersInPath($controllerPath);
        }

        return $result;
    }

    /**
     * Ambil semua controller dan action dari struktur yang sudah diproses.
     * @param array|null $items
     * @return array
     */
    public function getControllerActions($items = null)
    {
        if ($items === null) {
            $items = $this->getAllControllers();
        }

        foreach ($items as $module => $data) {
            foreach ($data['controllers'] as $controllerName => $controller) {
                $items[$module]['controllers'][$controllerName]['actions'] = $this->getActions($controller['path']);
            }
        }

        return $items;
    }

    /**
     * Ambil semua action dari file controller.
     * @param string $filePath
     * @return array
     */
    protected function getActions($filePath)
    {
        $actions = [];

        if (!is_file($filePath)) return $actions;

        $content = file_get_contents($filePath);
        preg_match_all('/public\s+function\s+action([A-Z][a-zA-Z0-9_]*)\s*\(/', $content, $matches, PREG_OFFSET_CAPTURE);

        foreach ($matches[1] as $match) {
            $actionName = strtolower($match[0]);
            $lineNumber = substr_count(substr($content, 0, $match[1]), "\n") + 1;
            $actions[$actionName] = [
                'name' => $actionName,
                'line' => $lineNumber,
            ];
        }

        return $actions;
    }

    /**
     * Ambil semua file controller dari path.
     * @param string $path
     * @return array
     */
    protected function getControllersInPath($path)
    {
        $controllers = [];

        if (!is_dir($path)) return $controllers;

        foreach (new \DirectoryIterator($path) as $file) {
            if ($file->isDot() || !$file->isFile()) continue;

            $filename = $file->getFilename();
            if (stripos($filename, 'Controller.php') === false) continue;

            $name = strtolower(substr($filename, 0, -14));
            $controllers[$name] = [
                'name' => $name,
                'file' => $filename,
                'path' => $file->getPathname(),
            ];
        }

        return $controllers;
    }
}
