<?php

/*
 * This file is part of the Eventum (Issue Tracking System) package.
 *
 * @copyright (c) Eventum Team
 * @license GNU General Public License, version 2 or later (GPL-2+)
 *
 * For the full copyright and license information,
 * please see the COPYING and AUTHORS files
 * that were distributed with this source code.
 */

namespace Eventum\Model\Entity;

use InvalidArgumentException;
use Setup;

/**
 * Class Eventum\Model\Entity\CommitRepo
 */
class CommitRepo
{
    /** @var array */
    private $config;

    public function __construct($name)
    {
        $setup = Setup::get();

        if (!isset($setup['scm'][$name])) {
            throw new InvalidArgumentException("SCM Repo '$name' not defined");
        }

        $this->config = $setup['scm'][$name];
    }

    public function getName()
    {
        return $this->config['name'];
    }

    /**
     * @return \Zend\Config\Config
     */
    public static function getAllRepos()
    {
        return Setup::get()->scm;
    }

    /**
     * Get CommitRepo from $repo_url
     * Walk over all configured scm to find one by matching url.
     *
     * @param array $repo_url
     * @return CommitRepo
     */
    public static function getRepoByUrl($repo_url)
    {
        foreach (static::getAllRepos() as $name => $scm) {
            if (!$scm->urls) {
                continue;
            }
            foreach ($scm->urls as $url) {
                if ($url == $repo_url) {
                    return new static($scm->name);
                }
            }
        }

        return null;
    }

    /**
     * The scm may be configured to accept only certain branches or exclude some
     *
     * 'only' - Defines a list of git refs which to accept
     * 'except' - Defines a list of git refs which not to accept
     *
     * These accept exact branch names.
     * This could be changed to glob in the future,
     * currently i'm only interested of 'master' branch commits
     *
     * @param string $branch
     * @return bool
     */
    public function branchAllowed($branch)
    {
        // 'only' present, check it
        if (count($this->config['only'])) {
            return in_array($branch, $this->config['only']->toArray());
        }

        // if 'except' present
        if (count($this->config['except'])) {
            return !in_array($branch, $this->config['except']->toArray());
        }

        return true;
    }

    public function getCheckoutUrl($checkin)
    {
        return $this->parseURL($this->config['checkout_url'], $checkin);
    }

    public function getDiffUrl($checkin)
    {
        return $this->parseURL($this->config['diff_url'], $checkin);
    }

    public function getLogUrl($checkin)
    {
        return $this->parseURL($this->config['log_url'], $checkin);
    }

    public function getChangesetUrl(Commit $commit)
    {
        $replace = array(
            '{CHANGESET}' => $commit->getChangeset(),
            '{PROJECT}' => $commit->getProjectName(),
            '{VERSION}' => $commit->getChangeset(),
        );

        return str_replace(array_keys($replace), array_values($replace), $this->config['changeset_url']);
    }

    /**
     * Method used to parse an user provided URL and substitute a known set of
     * placeholders for the appropriate information.
     *
     * @param   string $url The user provided URL
     * @return  string The parsed URL
     */
    private function parseURL($url, $checkin)
    {
        $url = str_replace('{PROJECT}', $checkin['project_name'], $url);
        $url = str_replace('{FILE}', $checkin['cof_filename'], $url);
        $url = str_replace('{OLD_VERSION}', $checkin['cof_old_version'], $url);
        $url = str_replace('{NEW_VERSION}', $checkin['cof_new_version'], $url);

        // the current version to look log from
        if ($checkin['added']) {
            $url = str_replace('{VERSION}', $checkin['cof_new_version'], $url);
        } elseif ($checkin['removed']) {
            $url = str_replace('{VERSION}', $checkin['cof_old_version'], $url);
        } else {
            $url = str_replace('{VERSION}', $checkin['cof_new_version'], $url);
        }

        return $url;
    }
}
