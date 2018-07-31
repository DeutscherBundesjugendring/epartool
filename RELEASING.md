# RELEASING

To create a release:

1. Create a tag with the desired version. We follow [semver 2.0](https://semver.org/). Prereleases are denominated as `rc`. Examples: `v4.11.2`, `v4.2.0-rc.2`
2. Login into Jenkins and run build on the master branch.
3. Go to the [releases page](https://github.com/DeutscherBundesjugendring/epartool/releases), open the release detail and click `Edit Tag`. Then do the following: 

    * Update release description summerizing all changes that took place since the last release.
    * If the relese is a prerelease (`rc`) mark it as such with the provided checkbox.
    * Save the changes
    
You are done!
   
 


