<?php

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TeamPasswordBackupCommand
 */
class TeamPasswordBackupDecryptCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('teampassword:backup:decrypt');
        $this->setDescription('Decrypt an teampassword.com backup.');
        $this->addArgument('backup-file', InputArgument::REQUIRED, 'The teampassword.com backup file.');
        $this->addArgument('private-key', InputArgument::REQUIRED, 'The encrypted private-key file.');
        $this->addArgument('password', InputArgument::REQUIRED, 'The password for the encrypted private-key.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $backup     = @file_get_contents($input->getArgument('backup-file'));
        $privateKey = @file_get_contents($input->getArgument('private-key'));
        $password   = $input->getArgument('password');

        if ($backup === false) {
            throw new \InvalidArgumentException('Could not load backup-file.');
        }
        if ($privateKey === false) {
            throw new \InvalidArgumentException('Could not load private-key.');
        }

        $backup = json_decode($backup, true);
        $result = array();

        foreach ($backup as $account) {
            throw new \RuntimeException('Decryption not implemented.');
            openssl_private_decrypt(
                base64_decode($account['encrypted_key']),
                $decrypted,
                openssl_pkey_get_private($account['encrypted_key'], $password)
            );
            $result[] = $account;
        }


        $output->writeln("Successfully decrypted teampassword.com backup:" . print_r($result, true));
    }
}