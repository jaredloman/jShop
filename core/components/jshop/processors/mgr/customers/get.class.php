<?php
/**
* jShop
*
* Copyright 2013 by Jared Loman <jared@jaredloman.com>
*
* This file is part of jShop, a simple shopping component for MODx Revolution.
*
* jShop is free software; you can redistribute it and/or modify it under the
* terms of the GNU General Public License as published by the Free Software
* Foundation; either version 2 of the License, or (at your option) any later
* version.
*
* jShop is distributed in the hope that it will be useful, but WITHOUT ANY
* WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
* A PARTICULAR PURPOSE. See the GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License along with
* EasyInventory; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
* Suite 330, Boston, MA 02111-1307 USA
*
* @package jshop
*/
/**
* @package jshop
* @subpackage processor
*/
class modUserGetProcessor extends modObjectGetProcessor {
    public $classKey = 'modUser';
    public $languageTopics = array('user');
    public $permission = 'view_user';
    public $objectType = 'user';

    public function beforeOutput() {
        if ($this->getProperty('getGroups',false)) {
            $this->getGroups();
        }
        return parent::beforeOutput();
    }

    /**
     * Get all the groups for the user
     * @return array
     */
    public function getGroups() {
        $c = $this->modx->newQuery('modUserGroupMember');
        $c->select($this->modx->getSelectColumns('modUserGroupMember','modUserGroupMember'));
        $c->select(array(
            'role_name' => 'UserGroupRole.name',
            'user_group_name' => 'UserGroup.name',
        ));
        $c->leftJoin('modUserGroupRole','UserGroupRole');
        $c->innerJoin('modUserGroup','UserGroup');
        $c->where(array(
            'member' => $this->object->get('id'),
        ));
        $c->sortby('modUserGroupMember.rank','ASC');
        $members = $this->modx->getCollection('modUserGroupMember',$c);

        $data = array();
        /** @var modUserGroupMember $member */
        foreach ($members as $member) {
            $roleName = $member->get('role_name');
            if ($member->get('role') == 0) { $roleName = $this->modx->lexicon('none'); }
            $data[] = array(
                $member->get('user_group'),
                $member->get('user_group_name'),
                $member->get('member'),
                $member->get('role'),
                empty($roleName) ? '' : $roleName,
                $this->object->get('primary_group') == $member->get('user_group') ? true : false,
                $member->get('rank'),
            );
        }
        $this->object->set('groups','(' . $this->modx->toJSON($data) . ')');
        return $data;
    }

    public function cleanup() {
        $userArray = $this->object->toArray();

        $profile = $this->object->getOne('Profile');
        if ($profile) {
            $userArray = array_merge($profile->toArray(),$userArray);
        }

        $userArray['dob'] = !empty($userArray['dob']) ? strftime('%m/%d/%Y',$userArray['dob']) : '';
        $userArray['blockeduntil'] = !empty($userArray['blockeduntil']) ? strftime('%Y-%m-%d %H:%M:%S',$userArray['blockeduntil']) : '';
        $userArray['blockedafter'] = !empty($userArray['blockedafter']) ? strftime('%Y-%m-%d %H:%M:%S',$userArray['blockedafter']) : '';
        $userArray['lastlogin'] = !empty($userArray['lastlogin']) ? strftime('%m/%d/%Y',$userArray['lastlogin']) : '';

        unset($userArray['password'],$userArray['cachepwd']);
        return $this->success('',$userArray);
    }
}
return 'modUserGetProcessor';