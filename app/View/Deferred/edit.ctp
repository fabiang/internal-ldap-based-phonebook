<?php
    /**
     * This file is the view file of the application. Used for editing
     *  deferred save.
     *
     * InternalPhonebook: Internal phone book based on content of Active Directory.
     * @copyright Copyright 2017, Andrey Klimov.
     * @package app.View.Deferred
     */

    echo $this->AssetCompress->css('CakeTheme.fileupload', ['block' => 'css']);
    echo $this->AssetCompress->script('CakeTheme.fileupload-image-min', ['block' => 'script']);
    echo $this->AssetCompress->script('CakeTheme.fileupload', ['block' => 'script']);
    echo $this->AssetCompress->script('CakeTheme.fileupload-image', ['block' => 'script']);
    echo $this->AssetCompress->script('CakeTheme.fileupload-i18n-' . $uiLcid2, ['block' => 'script']);

    $this->assign('title', __('Editing deferred save'));

    $this->Html->addCrumb(__('Deferred saves'), ['controller' => 'deferred', 'action' => 'index']);
    $displayName = $this->request->data('EmployeeEdit.' . CAKE_LDAP_LDAP_ATTRIBUTE_DISPLAY_NAME);
if (!empty($displayName)) {
    $this->Html->addCrumb(
        $this->Text->truncate(h($displayName), 20),
        ['controller' => 'deferred', 'action' => 'view', $id]
    );
}
    $this->Html->addCrumb(__('Editing'));
?>
    <div class="container">
<?php
        echo $this->ViewExtension->headerPage($pageHeader);
?>  
        <div class="row">
            <div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">          
<?php
        echo $this->element('formEmployeeEditFull', compact(
            'dn',
            'managers',
            'departments',
            'fieldsLabel',
            'fieldsLabelAlt',
            'fieldsInputMask',
            'fieldsInputTooltip',
            'readOnlyFields',
            'maxLinesMultipleValue',
            'changedFields',
            'maxfilesize',
            'acceptfiletypes',
            'employeePhoto',
            'guid',
            'forceDeferred'
        ));
?>
            </div>
        </div>
    </div>
