<?php
    /**
     * This file is the view file of the application. Used for checking
     *  state list of departments.
     *
     * InternalPhonebook: Internal phone book based on content of Active Directory.
     * @copyright Copyright 2017, Andrey Klimov.
     * @package app.View.Departments
     */

    $this->assign('title', __('Checking state list of departments'));

    $this->Html->addCrumb(__('Departments'), ['controller' => 'departments', 'action' => 'index']);
    $this->Html->addCrumb(__('Checking the list'));
?>  
    <div class="container">
<?php
        echo $this->ViewExtension->headerPage($pageHeader, $headerMenuActions);
        $urlNode = [
            'controller' => 'departments',
            'action' => 'view',
        ];
        echo $this->element('CakeTheme.tableTreeState', compact('treeState', 'urlNode'));
?>
    </div>
