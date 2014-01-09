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
        $this->addRole(new Zend_Acl_Role('guest'));
        $this->addRole(new Zend_Acl_Role('usr'), 'guest'); // usr erbt von guest
        $this->addRole(new Zend_Acl_Role('edt'), 'usr'); // edt erbt von usr usw.
        $this->addRole(new Zend_Acl_Role('adm'), 'edt');
        $this->allow(null, null); // allen alles erlauben
        $this->deny('guest', 'admin'); // admin per default für alle verbieten
        $this->allow('edt','admin'); // admin für Editoren erlauben
        $this->allow('adm', 'admin'); // admin für Admins erlauben
    }
}
