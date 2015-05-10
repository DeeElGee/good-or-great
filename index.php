<?php
require_once 'EWSType.php';
require_once 'ExchangeWebServices.php';
require_once 'NTLMSoapClient.php';
require_once 'NTLMSoapClient/Exchange.php';
require_once 'EWSType/FindItemType.php';
//
require_once 'EWSType/ItemIdType.php';
require_once 'EWSType/NonEmptyArrayOfBaseItemIdsType.php';
require_once 'EWSType/GetItemType.php';
require_once 'EWSType/ItemResponseShapeType.php';
require_once 'EWSType/NonEmptyArrayOfBaseFolderIdsType.php';
require_once 'EWSType/DistinguishedFolderIdNameType.php';
require_once 'EWSType/DistinguishedFolderIdType.php';
require_once 'EWSType/DefaultShapeNamesType.php';
require_once 'EWSType/ItemQueryTraversalType.php';
require_once 'EWSType/IndexedPageViewType.php';
require_once 'EWSType/PathToUnindexedFieldType.php';
require_once 'EWSType/FieldURIOrConstantType.php';
require_once 'EWSType/ConstantValueType.php';
//ConstantValueType
$ews = new ExchangeWebServices("legacy.enginemail.com", "5gs.local\\David.Gordon", "flushM3!", ExchangeWebServices::VERSION_2010);

$request = new EWSType_FindItemType();
$request->ItemShape = new EWSType_ItemResponseShapeType();
$request->ItemShape->BaseShape = EWSType_DefaultShapeNamesType::ALL_PROPERTIES;

$request->Restriction->Contains->FieldURI = new EWSType_PathToUnindexedFieldType();
$request->Restriction->Contains->FieldURI->FieldURI = 'item:DisplayTo';
$request->Restriction->Contains->Constant->Value = 'goodorgreat @ wcrs';

$request->IndexedPageItemView = new EWSType_IndexedPageViewType();
$request->IndexedPageItemView->BasePoint = 'Beginning';
$request->IndexedPageItemView->Offset = 0;
$request->IndexedPageItemView->MaxEntriesReturned = 10;


$request->ParentFolderIds = new EWSType_NonEmptyArrayOfBaseFolderIdsType();
$request->ParentFolderIds->DistinguishedFolderId = new EWSType_DistinguishedFolderIdType();
$request->ParentFolderIds->DistinguishedFolderId->Id = EWSType_DistinguishedFolderIdNameType::INBOX;
// $request->ParentFolderIds->DistinguishedFolderId->Mailbox->EmailAddress = 'goodorgreat@wcrs.com';

$request->Traversal = EWSType_ItemQueryTraversalType::SHALLOW;

$result = $ews->FindItem($request);
//var_dump($result->ResponseMessages->FindItemResponseMessage->RootFolder->Items);
if ($result->ResponseMessages->FindItemResponseMessage->ResponseCode == 'NoError' && $result->ResponseMessages->FindItemResponseMessage->ResponseClass == 'Success'){
    $count = $result->ResponseMessages->FindItemResponseMessage->RootFolder->TotalItemsInView;
    echo '<h1>',$count,'</h1>';
    for ($i = 0; $i < 10; $i++){
        $message_id = $result->ResponseMessages->FindItemResponseMessage->RootFolder->Items->Message[$i]->ItemId->Id;
        $request = new EWSType_GetItemType();

        $request->ItemShape = new EWSType_ItemResponseShapeType();
        $request->ItemShape->BaseShape = EWSType_DefaultShapeNamesType::ALL_PROPERTIES;

        $request->ItemIds = new EWSType_NonEmptyArrayOfBaseItemIdsType();
        $request->ItemIds->ItemId = new EWSType_ItemIdType();
        $request->ItemIds->ItemId->Id = $message_id; 

        $response = $ews->GetItem($request);
        //print_r($response);exit;
        if( $response->ResponseMessages->GetItemResponseMessage->ResponseCode == 'NoError' &&
            $response->ResponseMessages->GetItemResponseMessage->ResponseClass == 'Success' ) {

            $message = $response->ResponseMessages->GetItemResponseMessage->Items->Message;

            //process the message data.
            var_dump($message);

        }

    }

}

// $con = imap_open("{legacy.enginemail.com:443}","5gs.local\\David.Gordon","flushM3!");
// var_dump($con);
