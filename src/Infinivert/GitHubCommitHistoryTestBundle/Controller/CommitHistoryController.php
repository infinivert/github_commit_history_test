<?php
namespace Infinivert\GitHubCommitHistoryTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CommitHistoryController extends Controller
{

    protected $GitHub;


    /**
     * Sets the GitHub client.
     * Used by Services to inject on instantiation.
     * May also be used in other contexts to override the GitHub client.
     * @param \Github\Client $gitHubClient [description]
     */
    public function setApiAction(\Github\Client $gitHubClient = null)
    {
    	if (!$gitHubClient) $gitHubClient = $this->get('github_api');
        $this->GitHub = $gitHubClient;
    }

    /**
     * Outputs the Index view
     * @return view
     */
    public function indexAction()
    {
    	if (empty($this->GitHub)) $this->setApiAction();
        exit('<pre>' . print_r($this->GitHub, true) . '</pre>');

        //Check YAML file for org name and optional credentials
        //Authenticate if required
        //Request org info
        //Pass results to appropriate view
        return $this->render('InfinivertGitHubCommitHistoryTestBundle:CommitHistory:index.html.twig');
    }
}
