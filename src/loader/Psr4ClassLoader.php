<?php
declare(strict_types=1);

namespace loader;

class Psr4ClassLoader {
    private string $prefix;
    private string $root;

    public function __construct(string $prefix, string $root) {
        $this->prefix = $prefix;
        $this->root = $root;
    }

    public function loadClass(string $classname): void {
        $len = strlen($this->prefix);

        if (strpos($classname, $this->prefix) !== 0) {
            return;
        }

        $relativeClass = substr($classname, $len);
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass);
        $filename = rtrim($this->root, '/\\') . DIRECTORY_SEPARATOR . $path . '.php';

        if (is_file($filename)) {
            require_once $filename;
        }
    }

    public function register(): void {
        spl_autoload_register([$this, 'loadClass']);
    }
}
