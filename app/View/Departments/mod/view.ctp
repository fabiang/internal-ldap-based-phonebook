<?php
    /**
     * This file is the view file of the application. Used for viewing
     *  information of department in modal window.
     *
     * InternalPhonebook: Internal phone book based on content of Active Directory.
     * @copyright Copyright 2017, Andrey Klimov.
     * @package app.View.Departments.mod
     */

    echo $this->ViewExtension->headerPage($pageHeader, $headerMenuActions);
    echo $this->element('infoDepartment', compact('department'));
