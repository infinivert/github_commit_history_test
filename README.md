##GitHub Commit History Test
==========================

Sample Symfony project for pulling an organization's commit statistics from the GitHub API.

Introduction
------------

This project is a demo application that can communicate with the GitHub API without credentials, or optionally, with username and password credentials and retrieve the number of commits, additions, and deletions for each of an organization's repositories along with the totals across all repositories.

Installation
------------

Clone the project :

	git clone git@github.com:infinivert/github_commit_history_test.git HistoryTest

Update packages :

	cd HistoryTest
	composer.phar install

Modify the Organization name and add optional login credentials (recommended) :

	vim src/Infinivert/GitHumCommitHistoryTestBundle/Resources/config/config.yml

Run the built-in webserver :

	app/console server:run

Open the app in your browser of choice :

	http://127.0.0.1:8000/
