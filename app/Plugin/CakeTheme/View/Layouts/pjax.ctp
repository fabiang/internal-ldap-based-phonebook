<?php
/**
 * This file is the layout file of view the plugin. Used for render template
 *  of page for PJAX request.
 *
 * CakeTheme: Set theme for application.
 * @copyright Copyright 2016, Andrey Klimov.
 * @package plugin.View.Layouts
 */

if (!isset($isExternalAuth)) {
    $isExternalAuth = false;
}

if (!isset($emailContact)) {
    $emailContact = '';
}

if (!isset($emailSubject)) {
    $emailSubject = '';
}

if (!isset($showBreadcrumb)) {
    $showBreadcrumb = true;
}
?>  
    <div id="header">   
<?php
    echo $this->element('barNav', compact('isExternalAuth', 'emailContact', 'emailSubject'));
if ($showBreadcrumb) {
    echo $this->Html->div('breadcrumb hidden-print', $this->Html->getCrumbs(
        '&nbsp;' .
        $this->Html->tag('span', '', ['class' => 'fas fa-angle-right']) . '&nbsp;',
        [
            'text' => $this->Html->tag('span', '', ['class' => 'fas fa-home']),
            'url' => '/',
            'escape' => false
        ]
    ));
}
?>
    </div>
    <div id="content">
<?php
    echo $this->fetch('content');
?>
    </div>