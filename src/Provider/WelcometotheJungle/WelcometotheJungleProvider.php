<?php

namespace App\Provider\WelcometotheJungle;

use App\Entity\Job;
use App\Job\EmploymentType;
use App\Provider\JobCollection;
use App\Provider\JobProviderInterface;
use App\Provider\SearchParameters;

final class WelcometotheJungleProvider implements JobProviderInterface
{
    public const SOURCE_NAME = 'WTTJ';

    public function __construct(private readonly WelcometotheJungleClient $client)
    {
    }

    public function retrieve(SearchParameters $parameters): JobCollection
    {
        $jobCollection = new JobCollection();

        $data = $this->client->crawl();

        foreach ($data as $datum) {
            $job = new Job();
            $job->setLocation($datum['location']);
            $job->setTitle($datum['title']);
            $job->setOrganization($datum['company']);
            $job->setOrganizationImageUrl($datum['companyLogo'] ?? null);
            $job->setSource(self::SOURCE_NAME);
            $job->setTags(['PHP', 'Symfony']);
            $job->setUrl($datum['url']);

            switch ($datum['employmentType']) {
                case 'FULL_TIME':
                    $job->setEmploymentType(EmploymentType::FULL_TIME);
                    break;
                case 'CONTRACTOR':
                    $job->setEmploymentType(EmploymentType::INTERNSHIP);
            }
            $job->publish();

            $jobCollection->addJob($job);
        }

        return $jobCollection;
    }

    public function enabled(): bool
    {
        return true;
    }
}
