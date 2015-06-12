<?php
/**
 * Created by PhpStorm.
 * User: Max
 * Date: 6/12/15
 * Time: 2:17 PM
 */

/*
 * Construct a tree recursively based on json data
 */
class Portal_Model_UserGroups extends HealthGuru_Model
{
    public function regenerateTree($cids = array(), $userId = '', $useDefault = true) {
        if (empty($userId)) {
            return 0;
        }
        $ucModel = HealthGuru_Manager::getModel('UserContent', 'Portal');

        if (!$useDefault) {
            $library = $ucModel->getRows(array(
                'where' => array(
                    'user_id' => $userId,
                    'status' => 1,
                    'type' => 'library'
                ),
                'meta' => HealthGuru_Model::META_FOR_IN,
                'limit' => 1,
                'result_type' => HealthGuru_Model::RESULT_TYPE_ASSOC
            ));

            $library = array_shift($library);

            if (!empty($library['meta']['tree']) && !$useDefault) {
                $treeTemplate = json_decode($library['meta']['tree'], true);
            }
        } else if ($useDefault) {
            $vault = $this->getRows(array(
                'where' => array(
                    'user_id' => $userId,
                    'status' => 1,
                    'type' => 'vault',
                    'parent_id' => 0,
                    'title' => 'My Video Vault'
                ),
                'limit' => 1,
                'result_type' => HealthGuru_Model::RESULT_TYPE_ASSOC
            ));

            $vault = array_shift($vault);
            $treeTemplate = array(
                array(
                    'property' => array(
                        'name' => 'My Library'
                    ),
                    'data' => array(
                        'gid' => 'my_library'
                    ),
                    'type' => 'folder',
                    'children' => array(
                        array(
                            'property' => array(
                                'name' => 'Healthguru Vault',
                                'checkbox' => 'checked'
                            ),
                            'type' => 'folder',
                            'data' => array(
                                'gid' => 'hg_vault'
                            ),
                            'children' => HealthGuru_Manager::getModel('Groups')->getJsonSitemap('off')
                        ),
                        array(
                            'property' => array(
                                'name' => 'My Video Vault',
                                'checkbox' => 'checked'
                            ),
                            'type' => 'folder',
                            'data' => array(
                                'gid' => 'p_' . $vault['idusergroup']
                            ),
                            'children' => array()
                        )
                    )

                )
            );
        }

        $cids = is_array($cids) ? $cids : array($cids);
        $this->_cidsFromContentSet = $cids;

        return $this->iterateTree($treeTemplate);
    }

    public function iterateTree($treeTemplate = array(), $staticity = false, $addedCids = array()) {
        $tree = array();

        $allSiblingsSelected = true;

        foreach ($treeTemplate as $node) {
            if ($node['type'] == 'folder' || ($node['type'] != 'folder' && !in_array($node['data']['cid'], $this->_cidsFromContentSet))) {
                $allSiblingsSelected = false;
            }
        }

        foreach ($treeTemplate as $key => $group) {

            $tree[$key] = array(
                'property' => array(
                    'name' => $group['property']['name'],
                    'checkbox' => 'unchecked' //default value to be modified later
                ),
                'type' => 'folder',
                'data' => array(
                    'gid' => $group['data']['gid'],
                    'parent_id' => $group['data']['parent_id']
                ),
                'children' => array()
            );

            if (!empty($group['children'])) {
                $tree[$key]['children'] = $this->iterateTree($group['children'], $staticity, $addedCids);

                $checked = '';
                if ($group['type'] == 'folder') {
                    $this->_leafCounter = array('checked' => 0, 'total' => 0);
                    $this->_recursiveCheckbox($tree[$key]['children']);
                    if ($this->_leafCounter['checked'] === 0) {
                        $checked = 'unchecked';
                    } else if ($this->_leafCounter['checked'] < $this->_leafCounter['total']) {
                        $checked = 'partially';
                    } else if ($this->_leafCounter['checked'] == $this->_leafCounter['total']) {
                        $checked = 'checked';
                    }
                }

                $tree[$key]['property']['checkbox'] = $checked;

            } else {
                $tree[$key]['type'] = $group['type'];
                $tree[$key]['data']['cid'] = $group['data']['cid'];
                $tree[$key]['data']['mature'] = $group['data']['mature'];
                unset($tree[$key]['data']['gid']);

                if ($staticity) {
                    if ($group['type'] != 'folder' && !in_array($group['data']['cid'], $addedCids) && in_array($group['data']['cid'], $this->_cidsFromContentSet ?: array())) {
                        $tree[$key]['property']['checkbox'] = 'checked';
                    } else {
                        $tree[$key]['property']['checkbox'] = 'unchecked';
                    }
                } else {
                    if (in_array($group['data']['cid'], $addedCids) && $allSiblingsSelected) {
                        $tree[$key]['property']['checkbox'] = 'checked';
                    } else if (!in_array($group['data']['cid'], $addedCids) && in_array($group['data']['cid'], $this->_cidsFromContentSet ?: array())) {
                        $tree[$key]['property']['checkbox'] = 'checked';
                    } else {
                        $tree[$key]['property']['checkbox'] = 'unchecked';
                    }
                }
            }
        }
        return $tree;
    }
}