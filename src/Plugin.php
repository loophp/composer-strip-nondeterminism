<?php

declare(strict_types=1);

namespace loophp\ComposerStripNondeterminism;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use loophp\ComposerStripNondeterminism\Command\HashPath;
use loophp\ComposerStripNondeterminism\Service\TouchUtils;

final class Plugin implements PluginInterface, Capable, CommandProvider, EventSubscriberInterface
{
    public function activate(Composer $composer, IOInterface $io): void {}

    public function deactivate(Composer $composer, IOInterface $io): void {}

    public function getCapabilities(): array
    {
        return [
            \Composer\Plugin\Capability\CommandProvider::class => self::class,
        ];
    }

    public function getCommands(): array
    {
        return [
            new HashPath()
        ];
    }

    public function uninstall(Composer $composer, IOInterface $io) {}

    public static function getSubscribedEvents()
    {
        return [
            'post-autoload-dump' => 'touchVendorDir',
            'post-install-cmd' => 'touchVendorDir',
            'post-update-cmd' => 'touchVendorDir',
        ];
    }

    public function touchVendorDir(Event $event): void {
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');

        (new TouchUtils)->touch($vendorDir, 0, 0);
    }
}
