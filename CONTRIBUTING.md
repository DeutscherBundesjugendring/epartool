# CONTRIBUTING

We are glad to receive pull requests and we encourage people to contribute. However in order to keep things sane, please do follow the bellow defined rules and workflow for contributing.

## General rules

* Each issue and PR should fix only one well defined thing.
* Do not include in your PR fixes in places that are not directly relevant to the main issue. If you, for example, want to improve the coding style on code you would not touch otherwise make it a separate PR.
* Generally no commented out code is acceptable. If you REALLY need to comment out code make sure to add your email, date and reason why you are commenting it out. If relevant, feel free to include a link to an issue, release notes, discussion etc.

## Workflow

1. If you are working on a bigger task make sure an [issue exists](https://github.com/DeutscherBundesjugendring/epartool/issues). This way we will be able to have an idea of what people are working on. If needed it will be the place where discussion can take place. Minor and trivial tasks do not need an issue.

2. [Fork](http://help.github.com/fork-a-repo/) the DeutscherBundesjugendring/epartool repo.

3. Clone the repo to your machine:

    `$ git clone git@github.com:YOUR-GITHUB-USERNAME/epartool.git`

4. Add the DeutscherBundesjugendring/epartool repo as a new remote:

    `$ git remote add upstream git@github.com:DeutscherBundesjugendring/epartool.git`

5. Make sure your master is up to date:

    `$ git pull upstream master`

6. Create new feature branch:

    `$ git checkout -b name-of-your-branch-goes-here`

7. Make changes to code.

8. Add your changes to the git staging area:

    `$ git add path/to/my/file.php`

9. Push your changes to server

    `$ git push --set-upstream origin name-of-your-branch-goes-here`

10. Open a pull request from your feature branch to the DeutscherBundesjugendring/epartool master 

Thanks for contributing!
