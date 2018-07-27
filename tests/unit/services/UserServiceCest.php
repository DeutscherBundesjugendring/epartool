<?php

require APPLICATION_PATH . '/services/User.php';

class UserServiceCest
{
    /**
     * @param UnitTester $I
     */
    public function testGenerateName(UnitTester $I): void
    {
        foreach ($this->getGenerateNameData() as $email => $name) {
            $I->assertEquals($name, Service_User::generateName($email));
        }
    }

    /**
     * @return array
     */
    private function getGenerateNameData(): array
    {
        return [
            'johndoe@excample.com' => 'Johndoe',
            'john.doe@excample.com' => 'John Doe',
            'john.doe.smith@excample.com' => 'John Doe Smith',
            'john_doe@excample.com' => 'John Doe',
            'john_doe_smith@excample.com' => 'John Doe Smith',
            'john-doe@excample.com' => 'John Doe',
            'john-doe-smith@excample.com' => 'John Doe Smith',

            // Check mixed format
            'john.doe_smith-karl@excample.com' => 'John Doe_smith-karl',
            'john_doe-smith@excample.com' => 'John Doe-smith',
        ];
    }
}
