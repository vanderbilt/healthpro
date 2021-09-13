<?php

namespace App\Service;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class JiraService
{
    public const INSTANCE_URL = 'https://precisionmedicineinitiative.atlassian.net';

    private $client;
    private const SOURCE_PROJECT_KEY = 'HPRO';
    private const DESTINATION_PROJECT_KEY = 'PD';
    private const DESTINATION_ISSUE_TYPE_ID = '10000'; // 10000 = story

    private static $appIds = [
        'Stable' => 'pmi-hpo-test',
        'Production' => 'healthpro-prod'
    ];

    public function __construct(ParameterBagInterface $params, LoggerInterface $logger)
    {
        if (!$params->has('jira_api_user') || !$params->has('jira_api_token')) {
            $logger->warning('Missing Jira API configuration. See config.yml.dist for details.');
            return;
        }
        $this->client = new Client([
            'base_uri' => self::INSTANCE_URL . '/rest/api/2/',
            'auth' => [$params->get('jira_api_user'), $params->get('jira_api_token')]
        ]);
    }

    public function getVersions(int $count = 10): array
    {
        $endpoint = sprintf('project/%s/version', self::SOURCE_PROJECT_KEY);
        $response = $this->client->request('GET', $endpoint, [
            'query' => [
                'orderBy' => '-releaseDate',
                'maxResults' => $count,
                'expand' => 'issuesstatus'
            ]
        ]);
        $responseObject = json_decode($response->getBody()->getContents());

        return $responseObject->values ?? [];
    }

    public function getIssuesByVersion(string $version): array
    {
        $jql = sprintf('project=%s AND fixVersion=%s', self::SOURCE_PROJECT_KEY, $version);
        $response = $this->client->request('GET', 'search', [
            'query' => [
                'jql' => $jql,
                'fields' => 'issuetype,status,summary,assignee'
            ]
        ]);
        $responseObject = json_decode($response->getBody()->getContents());

        return $responseObject->issues ?? [];
    }

    public function createReleaseTicket(string $title, string $description): ?string
    {
        $response = $this->client->request('POST', 'issue', [
            'json' => [
                'fields' => [
                    'summary' => $title,
                    'description' => $description,
                    'project' => [
                        'key' => self::DESTINATION_PROJECT_KEY
                    ],
                    'issuetype' => [
                        'id' => self::DESTINATION_ISSUE_TYPE_ID
                    ]
                ]
            ]
        ]);
        $responseObject = json_decode($response->getBody()->getContents());

        return $responseObject->key ?? null;
    }

    public function createApprovalRequestComment(string $ticketId, string $comment): ?string
    {
        try {
            $response = $this->client->request('POST', "issue/{$ticketId}/comment", [
                'json' => [
                    'body' => $comment
                ]
            ]);
            return $response && $response->getStatusCode() === 201 ? true : false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function attachDeployOutput(string $version, string $env, string $ticketId): ?string
    {
        $headers = [
            'Accept' => 'application/json',
            'X-Atlassian-Token' => 'no-check'
        ];
        // TODO
        $path = __DIR__ . '/../../../deploy_20210910_142556.txt';
        $appId = self::$appIds[$env];
        try {
            $response = $this->client->request('POST', "issue/{$ticketId}/attachments", [
                'headers' => $headers,
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => fopen($path, 'rb'),
                        'filename' => "{$appId}.release-{$version}.txt"
                    ]
                ]
            ]);
            return $response && $response->getStatusCode() === 200 ? true : false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
