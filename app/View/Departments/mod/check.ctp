<?php
    /**
     * This file is the view file of the application. Used for checking
     *  state list of departments.
     *
     * InternalPhonebook: Internal phone book based on content of Active Directory.
     * @copyright Copyright 2017, Andrey Klimov.
     * @package app.View.Departments
     */

    echo $this->ViewExtension->headerPage($pageHeader, $headerMenuActions);
?>
    <div class="row">
        <div class="col-xs-10 col-xs-offset-1 col-sm-10 col-sm-offset-1 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
<?php
        $urlNode = [
            'controller' => 'departments',
            'action' => 'view',
        ];
        echo $this->element('CakeTheme.tableTreeState', compact('treeState', 'urlNode'));
?>
        </div>
    </div>
