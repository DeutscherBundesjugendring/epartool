// Folder on server where the backup of the old site saved
def backupFolder = '~/deploy-backups'

// Name of the folder where the new application is being prepared.
// Must not exist in the application that is being deployed.
def deployFolder = "deployment"

// Name of the archive that contains the new application.
// Must not exist in the application that is being deployed.
def deployTar = 'deployment.tar.gz'

// Folder on server where the applications exist
def targetFolder = '~/webapps-data'

// A map of deployment environments.
// Each element is a map with the following keys:
//  - host: The ssh host url in format user@some.server.com
//  - folder: The folder where the app is located on server
//  - dbName: Name of the database to back up
//  - siteUrl: Url where the deployed site is accessible
def deployBranches = [
    develop: [
        host: 'synergic@lutra.visionapps.cz',
        folder: 'dbjr_dev',
        dbName: 'dbjr_dev',
        siteUrl: 'http://dbjr.dev.visionapps.cz',
        credentials: 'deploy_lutra',
    ],
]



node {
     try {
        stage('Checkout code') {
            timeout(1) {
                checkout scm
            }
        }

        stage('Prepare docker containers') {
            timeout(20) {
                sh 'docker-compose build tools'
                sh 'docker-compose up -d db'
                sleep 10
                sh 'docker-compose up -d tools'
            }
        }

        stage('Build app and run tests') {
            timeout(20) {
                sh 'rm -f application/configs/config.local.ini'
                sh 'rm -f application/configs/phinx.local.yml'
                sh 'cp application/configs/config.local-example.ini application/configs/config.local.ini'
                sh 'cp application/configs/phinx.local-example.yml application/configs/phinx.local.yml'
                sh 'docker-compose run tools bash -c "sh /root/init-container.sh /www && su www-data -c \'robo test:install\'"'
            }
        }

        if (deployBranches.containsKey(env.BRANCH_NAME)) {
            stage('Deploy') {
                timeout(5) {
                    createDeployTar(deployTar)
                    deploy(deployBranches[env.BRANCH_NAME], targetFolder, deployTar, deployFolder, backupFolder)
                    notifyOfDeploy(deployBranches, env.BRANCH_NAME)
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

def createDeployTar(deployTar) {
    sh """tar \
        -zcf ${deployTar} \
        --exclude=application/configs/config.local.ini \
        --exclude=application/configs/phinx.local.yml \
        --exclude=runtime/logs/* \
        --exclude=runtime/cache/* \
        --exclude=runtime/sessions/* \
        --exclude=www/index_dev.php \
        --exclude=www/index_test.php \
        --exclude=www/image_cache/* \
        --exclude=www/media/* \
        application bin data languages languages_zend library runtime vendor www RoboFile.php"""
}

def notifyOfDeploy(deployBranches, currentBranch) {
    echo 'DEPLOY SUCCESSFUL'
    slackSend(
        color: 'good',
        message: "DBJR: Větev `${currentBranch}` byla nasazena na: ${deployBranches[currentBranch].siteUrl} :sunny:"
    )
}
