<?php
/**
 * Plugin für Zend_Acl
 * @author Markus
 *
 */
class Plugin_Auth_Acl extends Zend_Acl
{
    /**
     * Constructor
     * Definiere Ressourcen
     * @todo Auslagern der Definitionen in INI-Datei
     * @todo Rechte verfeinern
     */
    public function __construct()
    {
        // RESSOURCES
        $this->add(new Zend_Acl_Resource('admin')); // admin Module (Adminbereich)
        $this->addRole(new Zend_Acl_Role(Model_Users::ROLE_GUEST));
        $this->addRole(new Zend_Acl_Role(Model_Users::ROLE_USER), Model_Users::ROLE_GUEST); // usr erbt von guest
        $this->addRole(new Zend_Acl_Role(Model_Users::ROLE_EDITOR), Model_Users::ROLE_USER); // edt erbt von usr usw.
        $this->addRole(new Zend_Acl_Role(Model_Users::ROLE_ADMIN), Model_Users::ROLE_EDITOR);
        $this->allow(null, null); // allen alles erlauben
        $this->deny(Model_Users::ROLE_GUEST, Model_Users::ROLE_ADMIN); // admin per default für alle verbieten
        $this->allow(Model_Users::ROLE_EDITOR,'admin'); // admin für Editoren erlauben
        $this->allow(Model_Users::ROLE_ADMIN, 'admin'); // admin für Admins erlauben
    }
}
