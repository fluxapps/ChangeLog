<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace srag\Plugins\ChangeLog\Component;

use ilAdministrationGUI;
use ilAuthUtils;
use ilException;
use ilObjUser;
use ilObjUserGUI;
use srag\Plugins\ChangeLog\LogEntry\Deletion\ChangeLogDeletionEntry;

/**
 * Class ChangeLogComponentUser
 *
 * @package srag\Plugins\ChangeLog\Component
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class ChangeLogComponentUser extends ChangeLogComponent
{

    const COMPONENT_ID = 'Services/User';
    protected $attributes
        = array(
            'auth_mode'        => 'getAuthMode',
            'login'            => 'getLogin',
            'user_ext_account' => 'getExternalAccount',
            'active'           => 'getActive',
            'lastname'         => 'getLastname',
            'firstname'        => 'getFirstname',
            //		'time_limit_unlimited' => 'getTimeLimitUnlimited',
            //		'time_limit_from' => 'getTimeLimitFrom',
            //		'time_limit_until' => 'getTimeLimitUntil',
            //		'gender' => 'getGender',
            'fullname'         => 'getFullname',
            //		'birthday' => 'getBirthday',
            'institution'      => 'getInstitution',
            //		'department' => 'getDepartment',
            //		'street' => 'getStreet',
            //		'city' => 'getCity',
            //		'zipcode' => 'getZipcode',
            //		'country' => 'getCountry',
            //		'selected_country' => 'getSelectedCountry',
            //		'phone_office' => 'getPhoneOffice',
            //		'phone_home' => 'getPhoneHome',
            //		'phone_mobile' => 'getPhoneMobile',
            //		'fax' => 'getFax',
            'email'            => 'getEmail',
            //		'hobby' => 'getHobby',
            //		'referral_comment' => 'getComment',
            //		'general_interests' => 'getGeneralInterestsAsText',
            //		'offering_help' => 'getOfferingHelpAsText',
            //		'looking_for_help' => 'getLookingForHelpAsText',
            //		'matriculation' => 'getMatriculation',
            //		'delicious' => 'getDelicious',
            //		'client_ip' => 'getClientIP',
            //		'language' => 'getLanguage',
        );
    //
    //	protected $im_ids = array(
    //		"im_icq",
    //		"im_yahoo",
    //		"im_msn",
    //		"im_aim",
    //		"im_skype",
    //		"im_jabber",
    //		"im_voip",
    //	);
    //
    //	protected $prefs = array(
    //		'skin',
    //		'hits_per_page',
    //		'show_users_online',
    //		'hide_own_online_status',
    //	);
    //

    /**
     * @inheritdoc
     */
    public function getModification(array $parameters)
    {
        return parent::getModification(array('object' => $parameters['user_obj']));
    }


    /**
     * @inheritdoc
     */
    public function getCreation(array $parameters)
    {
        return parent::getModification(array('object' => $parameters['user_obj']));
    }


    /**
     * Return the ChangeLogDeletionEntry object for this component
     *
     * @param array $parameters
     *
     * @return ChangeLogDeletionEntry
     */
    public function getDeletion(array $parameters)
    {
        /** @var ilObjUser $user_object */
        $user_object = $parameters["object"];
        $deletion = new ChangeLogDeletionEntry();
        $deletion->setObjId($user_object->getId());
        $user_ref_id = current(ilObjUser::_getAllReferences($user_object->getId()));
        $deletion->setContainerRefId($user_ref_id);
        $deletion->setContainerObjId($user_object->getId());
        $deletion->setContainerType('usr');
        //$deletion->setTitle('[' . $user_object->getLogin() . '] ' . $user_object->getFirstname() . ' ' . $user_object->getLastname());
        $deletion->setTitle(self::plugin()->translate("user_deleted", "", [$user_object->getId()]));
        $deletion->setType($user_object->getType());
        $deletion->setDescription(self::plugin()->translate("user_deleted_info"));

        return $deletion;
    }


    /**
     * Return a unique ID among all components
     *
     * @return string
     */
    public function getId()
    {
        return self::COMPONENT_ID;
    }


    /**
     * Return a title for this component
     *
     * @return string
     */
    public function getTitle()
    {
        return self::plugin()->translate("user");
    }


    /**
     * Return the Id to be stored as ChangeLogModificationEntry->obj_id, which will be used e.g. to create
     * a Link to the object inside the changelog tableGUI. Tries out getRefId, then getId on the object.     *
     *
     * @param $object
     *
     * @return mixed
     * @throws ilException
     */
    protected function getModificationObjId($object)
    {
        return $object->getId();
    }


    function formatValue($attribute, $value)
    {
        switch ($attribute) {
            case 'auth_mode':
                $active_auth_modes = ilAuthUtils::_getActiveAuthModes();

                return ilAuthUtils::getAuthModeTranslation($active_auth_modes[$value]);
                break;
            default:
                return $value;
        }
    }


    /**
     * Overwrite this and return array of form array('cmdClass' => ..., 'cmd' =>, 'attribute' => ...)
     * to enable title as link in the modification table gui
     *
     * @return array cmdClass, cmd and attribute for creation of links
     */
    public function getCtrlParameters()
    {
        return array(
            'cmdClass'  => array(ilAdministrationGUI::class, ilObjUserGUI::class),
            'cmd'       => 'view',
            'attribute' => 'obj_id'
        );
    }
}
