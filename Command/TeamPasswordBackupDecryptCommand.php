<?php

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

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
        $this->addOption('password', null, InputOption::VALUE_REQUIRED, 'The password for the encrypted private-key.', null);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $backup     = @file_get_contents($input->getArgument('backup-file'));
        $privateKey = @file_get_contents($input->getArgument('private-key'));
        $password   = $input->getOption('password');

        if (!$password) {
            $helper = $this->getHelper('question');
            $question = new Question('Please enter your password: ');
            $question->setHidden(true);
            $question->setHiddenFallback(false);

            $password = $helper->ask($input, $output, $question);
        }

        if ($backup === false) {
            throw new \InvalidArgumentException('Could not load backup-file.');
        }
        if ($privateKey === false) {
            throw new \InvalidArgumentException('Could not load private-key.');
        }

        $backup     = json_decode($backup, true);
        $result     = array();
        $privateKey = openssl_get_privatekey($privateKey, $password);

        if ($privateKey === false) {
            throw new \RuntimeException('Could not decrypt private key. Wrong password?');
        }

        foreach ($backup as $account) {

            @openssl_private_decrypt(
                base64_decode($account['encrypted_key']),
                $decryptedKey,
                $privateKey
            );

            if ($decryptedKey === null) {
                throw new \RuntimeException('The encryption-key can not be decrypted with the given private-key.');
            }

            $encryptedData = @json_decode($account['encrypted_json']);

            if ($encryptedData === null || !property_exists($encryptedData, 'data') || !property_exists($encryptedData, 'iv')) {
                throw new \RuntimeException('No data for decryption detected.');
            }

            $decryptedData = openssl_decrypt(
                base64_decode($encryptedData->data),
                'AES-256-CBC',
                $decryptedKey,
                OPENSSL_RAW_DATA||OPENSSL_ZERO_PADDING,
                base64_decode($encryptedData->iv)
            );

            if (!$decryptedData) {
                throw new \RuntimeException('Could not decrypt data.');
            }

            $account['decrypted_data'] = json_decode(utf8_encode($decryptedData), true);

            $result[] = $account;
        }

        $output->writeln("Successfully decrypted teampassword.com backup:" . print_r($result, true));
    }
}