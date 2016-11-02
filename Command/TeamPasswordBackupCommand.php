<?php

namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GuzzleHttp\Client;

/**
 * Class TeamPasswordBackupCommand
 */
class TeamPasswordBackupCommand extends Command
{
    /**
     * Constants
     */
    const TEAMPASSWORD_BASEURI   = 'https://app.teampassword.com';
    const TEAMPASSWORD_LOGIN_URL = '/api/session';
    const TEAMPASSWORD_VAULT     = '/api/accounts';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('teampassword:backup');
        $this->setDescription('Create a backup from teampassword.com and store it (including the private key) in the filesystem.');
        $this->addArgument('username', InputArgument::REQUIRED, 'The teampassword.com username for login.');
        $this->addArgument('password', InputArgument::REQUIRED, 'The teampassword.com password for login.');
        $this->addArgument('backup-directory', InputArgument::REQUIRED, 'The directory where to save teampassword.com backups and private-key.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $backupDir = $input->getArgument('backup-directory');
        $username  = $input->getArgument('username');
        $password  = $input->getArgument('password');
        $client    = new Client(array('base_uri' => self::TEAMPASSWORD_BASEURI, 'cookies'  => true));

        $loginResponse = $client->request(
            'POST',
            self::TEAMPASSWORD_LOGIN_URL,
            array('json' => array('email' => $username, 'password' => $password))
        );
        $loginResponse = json_decode($loginResponse->getBody()->getContents());

        if (!property_exists($loginResponse, 'encrypted_private_key')) {
            throw new \RuntimeException('Unexpected login response format. Private key is missing.');
        }

        $accountsJson = $client->request('GET', self::TEAMPASSWORD_VAULT)->getBody()->getContents();
        $accountObjs  = json_decode($accountsJson);

        if (   empty($accountObjs)
            || !property_exists($accountObjs[0], 'name')
            || !property_exists($accountObjs[0], 'url')
            || !property_exists($accountObjs[0], 'encrypted_json')
            || !property_exists($accountObjs[0], 'encrypted_key')
        ) {
            throw new \RuntimeException('Unexpected format of password collection response detected.');
        }

        $file = $backupDir . DIRECTORY_SEPARATOR . date("Y-m-d", time()) . '.json';
        if (@file_put_contents($file, $accountsJson) === false) {
            throw new \RuntimeException("Could not write output-file: {$file}");
        }

        $file = $backupDir . DIRECTORY_SEPARATOR . 'encrypted-key.pem';
        if (@file_put_contents($file, $loginResponse->encrypted_private_key) === false) {
            throw new \RuntimeException("Could not write output-file: {$file}");
        }
    }
}