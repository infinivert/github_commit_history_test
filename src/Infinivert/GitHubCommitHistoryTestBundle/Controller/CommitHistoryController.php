<?php
namespace Infinivert\GitHubCommitHistoryTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CommitHistoryController extends Controller
{

    private $GitHub;
    private $organization;
    private $username;
    private $password;

    /**
     * Outputs the Index view
     * @return view
     */
    public function indexAction()
    {
        // Ensure that the API is available
        if (empty($this->GitHub)) $this->setApi();

        // Assemble the data
        $data = $this->compileStats($this->organization);

        //Pass results to appropriate view
        return $this->render('InfinivertGitHubCommitHistoryTestBundle:CommitHistory:index.html.twig');
    }

    /**
     * Sets the GitHub client.
     * @param \Github\Client $gitHubClient
     */
    private function setApi(\Github\Client $gitHubClient = null)
    {
        // Get user configuration from config.yml
        $this->organization = $this->container->getParameter('organization');
        $this->username = $this->container->getParameter('username');
        $this->password = $this->container->getParameter('password');

        if (!$gitHubClient) $gitHubClient = $this->get('github_api');
        $this->GitHub = $gitHubClient;
        if (!empty($this->username) && !empty($this->password)) $this->GitHub->authenticate($this->username, $this->password, \Github\Client::AUTH_HTTP_PASSWORD);
    }

    /**
     * Compiles all the data needed for output
     * @param  string $organization Name of the organization being analyzed
     * @param  string $since        Only commits after this date will be returned. This is a timestamp in ISO 8601 format: YYYY-MM-DDTHH:MM:SSZ.
     * @param  string $until        Only commits before this date will be returned. This is a timestamp in ISO 8601 format: YYYY-MM-DDTHH:MM:SSZ.
     * @return array                All compiled output data
     */
    private function compileStats($organization, $since=null, $until=null)
    {
        if (!$since) $since = date('Y-m') . '-01T00:00:00' . date('P'); //YYYY-MM-DDTHH:MM:SSZ
        if (!$until) $until = date('Y-m-d\TH:i:sP');

        $data = array();

        $data['repos'] = $this->getOrgRepos($organization);

        $data['totals']['commits'] = 0;
        $data['totals']['additions'] = 0;
        $data['totals']['deletions'] = 0;

        foreach ($data['repos'] as $id => $repo) {
            $commits = $this->getRepoCommits($repo['owner'],$repo['name'],$since,$until);
            $data['repos'][$id]['commits'] = count($commits);
            $data['repos'][$id]['additions'] = 0;
            $data['repos'][$id]['deletions'] = 0;
            if (!empty($commits)) {
                foreach ($commits as $commit) {
                    $data['repos'][$id]['additions'] += $commit['additions'];
                    $data['repos'][$id]['deletions'] += $commit['deletions'];
                }
            }

            $data['totals']['commits'] += $data['repos'][$id]['commits'];
            $data['totals']['additions'] += $data['repos'][$id]['additions'];
            $data['totals']['deletions'] += $data['repos'][$id]['deletions'];
            
        }
        exit('<pre>'.print_r($data,TRUE).'</pre>');
    }

    /**
     * Retrieves and parses a list of repositories for an organization
     * @param  string $organization Name of the organization being analyzed
     * @return array                A list of repositories for an organization including owner and name
     */
    private function getOrgRepos($organization)
    {
        $rawdata = $this->GitHub->api('organization')->repositories($organization);
        $repos = array();
        if (!empty($rawdata)) {
            foreach ($rawdata as $raw) {
                $repos[$raw['id']]['owner'] = $raw['owner']['login'];
                $repos[$raw['id']]['name'] = $raw['name'];
            }
        }
        return $repos;
    }

    /**
     * Retrieves and parses a list of commits for a repository
     * @param  string $owner      Owner of the repository
     * @param  string $repository Name of the repository
     * @param  string $since      Only commits after this date will be returned. This is a timestamp in ISO 8601 format: YYYY-MM-DDTHH:MM:SSZ.
     * @param  string $until      Only commits before this date will be returned. This is a timestamp in ISO 8601 format: YYYY-MM-DDTHH:MM:SSZ.
     * @return array              A list of commits including number of additions and deletions from $this->getCommit()
     */
    private function getRepoCommits($owner, $repository, $since, $until)
    {
        $rawdata = $this->GitHub->api('repo')->commits()->all($owner,$repository,array('since'=>$since,'until'=>$until));
        $commits = array();
        if (!empty($rawdata)) {
            foreach($rawdata as $raw){
                $commits[$raw['sha']] = $this->getCommit($owner, $repository, $raw['sha']);
            }
        }
        return $commits;
    }

    /**
     * Retrieves and parses a single commit
     * @param  string $owner      Owner of the repository
     * @param  string $repository Name of the repository
     * @param  string $sha        SHA for the commit to be retrieved
     * @return array              Contains the number of additions and deletions for the commit
     */
    private function getCommit($owner, $repository, $sha)
    {
        $rawdata = $this->GitHub->api('repo')->commits()->show($owner,$repository,$sha);
        $commit = array();
        if (!empty($rawdata)) {
            $commit = $rawdata['stats'];
        }
        return $commit;
    }
}