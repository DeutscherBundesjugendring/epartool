// Folder on server where the backup of the old site saved
def backupFolder = '~/deploy-backups'

// Name of the folder where the new application is being prepared.
// Must not exist in the application that is being deployed.
def deployFolder = "deployment"

// Folder on server where the applications exist
def targetFolder = '~/webapps-data'

// Folder on server where the wizzards are uploaded to
def wizardTargetFolder = '~/webapps'

// A map of deployment environments.
// Each element is a map with the following keys:
//  - host: The ssh host url in format user@some.server.com
//  - folder: The folder where the app is located on server
//  - dbName: Name of the database to back up
//  - siteUrl: Url where the deployed site is accessible
def deployBranches = [
    master: [
        host: 'synergic@lutra.visionapps.cz',
        folder: 'dbjr_dev',
        dbName: 'dbjr_dev',
        siteUrl: 'http://dbjr.dev.visionapps.cz',
        credentials: 'deploy_lutra',
    ],
]

def wizardDeployBranches = [
    master: [
        host: 'synergicprod@felis.visionapps.cz',
        folder: 'epartool_download_web523',
        credentials: 'deploy_felis',
        downloadUrl: 'http://epartool.download.felis.visionapps.cz/'
    ],
]


node {
     try {
        stage('Checkout code') {
            timeout(1) {
                checkout([
                    $class: 'GitSCM',
                    branches: scm.branches,
                    doGenerateSubmoduleConfigurations: scm.doGenerateSubmoduleConfigurations,
                    extensions: [[$class: 'CloneOption', noTags: false, shallow: false, depth: 0, reference: '']],
                    userRemoteConfigs: scm.userRemoteConfigs,
                 ])
            }
        }

        stage('Prepare docker containers') {
            timeout(20) {
                sh 'docker-compose -f docker-compose-build.yml build web'
                sh 'docker-compose -f docker-compose-build.yml up -d web'
            }
        }

        stage('Build app and run tests') {
            timeout(20) {
                sh 'rm -f application/configs/config.local.ini'
                sh 'rm -f application/configs/phinx.local.yml'
                sh 'cp application/configs/config.local-example.ini application/configs/config.local.ini'
                sh 'cp application/configs/phinx.local-example.yml application/configs/phinx.local.yml'
                sh 'docker-compose exec -T --user www-data web bash -c "composer install --optimize-autoloader && vendor/bin/robo test:install"'

                version = sh(returnStdout: true, script: "git tag --list --points-at HEAD")
                if (version == '') {
                    version = 'develop'
                }
                echo "Build version is ${version}"
                writeFile(file: 'VERSION.txt', text: version, encoding: 'UTF-8')
            }
        }

        stage('Publish') {
            timeout(15) {
                if (wizardDeployBranches.containsKey(env.BRANCH_NAME)) {
                    sh 'docker-compose exec -T --user www-data web bash -c "php vendor/bin/robo create:install-zip && php vendor/bin/robo create:update-zip"'
                    publishWizards(wizardDeployBranches[env.BRANCH_NAME], wizardTargetFolder)
                    notifyOfDeploy(env.BRANCH_NAME, wizardDeployBranches[env.BRANCH_NAME].downloadUrl)
                }

                if (deployBranches.containsKey(env.BRANCH_NAME)) {
                    sh 'docker-compose exec -T --user www-data web bash -c "php vendor/bin/robo create:deploy-tar"'
                    deploy(deployBranches[env.BRANCH_NAME], targetFolder, deployTar, deployFolder, backupFolder)
                    notifyOfDeploy(env.BRANCH_NAME, deployBranches[env.BRANCH_NAME].siteUrl)
                }
            }
        }

    } catch (err) {
        currentBuild.result = 'FAILURE'
        echo "BUILD ERROR: ${err.toString()}"
        emailext (
            recipientProviders: [[$class: 'DevelopersRecipientProvider']],
            subject: "Build ${env.JOB_NAME} [${env.BUILD_NUMBER}] failed",
            body: err.toString(),
            attachLog: true,
        )
        if (deployBranches.containsKey(env.BRANCH_NAME)) {
            slackSend(color: 'danger', message: "DBJR: Build a nasazení větve `${env.BRANCH_NAME}` selhaly :thunder_cloud_and_rain:")
        }

    } finally {
        stage('Cleanup') {
            timeout(5) {
                sh 'docker-compose stop'
                sh 'docker-compose rm --all --force'
            }
        }
    }
}



def deploy(deployBranch, targetFolder, deployTar, deployFolder, backupFolder) {
    sshagent (credentials: [deployBranch.credentials]) {
        sh """ssh ${deployBranch.host} /bin/bash << EOF
            cd ${targetFolder}/${deployBranch.folder}

            echo 'Creating deploy folder'
            rm -rf ${deployFolder}
            mkdir ${deployFolder}
            mkdir --parents ${deployFolder}/application/configs
            cp application/configs/config.local.ini ${deployFolder}/application/configs
            cp application/configs/phinx.local.yml ${deployFolder}/application/configs
            mkdir --parents ${deployFolder}/www
            cp -r www/media ${deployFolder}/www/media
            mkdir --parents ${deployFolder}/runtime
            cp -r runtime/logs ${deployFolder}/runtime/logs
            cp php.ini ${deployFolder}/php.ini
            cp .htaccess ${deployFolder}/.htaccess
            cp .htpasswd ${deployFolder}/.htpasswd 2>/dev/null || :

            echo 'Moving deploy folder to targetFolder'
            rm -rf ~/${targetFolder}/${deployBranch.folder}.deploy
            mv ${targetFolder}/${deployBranch.folder}/${deployFolder} ${targetFolder}/${deployBranch.folder}.deploy
        """

        sh "scp ${deployTar} ${deployBranch.host}:${targetFolder}/${deployBranch.folder}.deploy"

        sh """ssh ${deployBranch.host} /bin/bash << EOF
            cd ${targetFolder}/${deployBranch.folder}.deploy

            echo 'Extracting deployTar'
            tar -mxzf ${deployTar}

            echo 'Backing up old deploy'
            cp application/static/503.php ${targetFolder}/${deployBranch.folder}/www/index.php
            mysqldump -h 127.0.0.1 ${deployBranch.dbName} > ${targetFolder}/${deployBranch.folder}/db_dump.sql
            rm -rf ${backupFolder}/${deployBranch.folder}
            mv ${targetFolder}/${deployBranch.folder} ${backupFolder}/${deployBranch.folder}

            echo 'Running post upload Robo tasks'
            ~/bin/robo deploy:finalize

            echo 'Switching to new deploy'
            mv ${targetFolder}/${deployBranch.folder}.deploy ${targetFolder}/${deployBranch.folder}
            rm -f ${targetFolder}/${deployBranch.folder}/${deployTar}
        """
    }
}

def publishWizards(wizardDeployBranch, wizardTargetFolder) {
    version = readFile('VERSION.txt').trim()
    installZipFileName="ePartool-install_${version}.zip"
    updateZipFileName="ePartool-update_${version}.zip"

    sshagent (credentials: [wizardDeployBranch.credentials]) {
        sh "scp ${installZipFileName} ${wizardDeployBranch.host}:${wizardTargetFolder}/${wizardDeployBranch.folder}"
        sh "scp ${updateZipFileName} ${wizardDeployBranch.host}:${wizardTargetFolder}/${wizardDeployBranch.folder}"
    }

    sh "rm ${installZipFileName} ${updateZipFileName}"
}

def notifyOfDeploy(currentBranch, target) {
    echo 'DEPLOY SUCCESSFUL'
    slackSend(
        color: 'good',
        message: "ePartool: Branch `${currentBranch}` was deployed to: ${target} :sunny:"
    )
}
