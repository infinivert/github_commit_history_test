<?php

namespace Infinivert\GitHubCommitHistoryTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('InfinivertGitHubCommitHistoryTestBundle:Default:index.html.twig');
    }
}
