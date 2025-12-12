<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * CreateMissingSubscriptions command.
 *
 * Creates trial subscriptions for tenants that don't have any.
 */
class CreateMissingSubscriptionsCommand extends Command
{
    /**
     * @inheritDoc
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser
            ->setDescription('Create trial subscriptions for tenants without any subscription.')
            ->addOption('dry-run', [
                'short' => 'd',
                'boolean' => true,
                'default' => false,
                'help' => 'Show what would be done without actually creating subscriptions.',
            ]);

        return $parser;
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $dryRun = $args->getOption('dry-run');

        $tenantsTable = $this->fetchTable('Tenants');
        $abbonamentiTable = $this->fetchTable('Abbonamenti');

        // Find all active tenants
        $tenants = $tenantsTable->find()
            ->where(['is_active' => true])
            ->all();

        $created = 0;
        $skipped = 0;

        foreach ($tenants as $tenant) {
            // Check if tenant has any subscription
            $hasSubscription = $abbonamentiTable->find()
                ->where(['tenant_id' => $tenant->id])
                ->count() > 0;

            if ($hasSubscription) {
                $io->verbose("Tenant '{$tenant->nome}' (ID: {$tenant->id}) - already has subscription, skipping.");
                $skipped++;
                continue;
            }

            $io->out("Tenant '{$tenant->nome}' (ID: {$tenant->id}) - no subscription found.");

            if (!$dryRun) {
                $abbonamento = $tenantsTable->createTrialSubscription($tenant->id);
                if ($abbonamento) {
                    $io->success("  -> Created trial subscription (expires: {$abbonamento->data_fine->format('d/m/Y')})");
                    $created++;
                } else {
                    $io->error("  -> Failed to create subscription!");
                }
            } else {
                $io->info("  -> [DRY-RUN] Would create trial subscription");
                $created++;
            }
        }

        $io->out('');
        $io->out("Summary:");
        $io->out("  - Tenants processed: " . $tenants->count());
        $io->out("  - Subscriptions created: {$created}");
        $io->out("  - Tenants skipped (already have subscription): {$skipped}");

        if ($dryRun) {
            $io->warning("This was a dry run. Use without --dry-run to actually create subscriptions.");
        }

        return static::CODE_SUCCESS;
    }
}
