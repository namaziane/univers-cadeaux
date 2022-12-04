<?php

class GelatoPageFactory
{
    public static function create($pageId): GelatoPage
    {
        switch ($pageId) {
            case GelatoPage::PAGE_ID_STATUS:
                return new GelatoStatusPage((new GelatoStatusChecker()));
            default:
                return new GelatoMainPage();
        }
    }
}
