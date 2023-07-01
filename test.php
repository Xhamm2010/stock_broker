<?php

require_once('config.php');

$Settings = new Settings(SETTING_FILE, true);
$DbConnect = DbConnect::getInstance(SETTING_FILE);
$Query = new Query();

// TblStock::createTable();
// $TblStock = new TblStock();
// $stocks = ['gtbank', 'dangcem', 'mtn'];
// foreach ($stocks as $aStock) {
//     $col = [TblStock::NAME=>[$aStock, 'isValue']];
//     var_dump($TblStock->insert($col));
// }


$broker = new Broker();
$broker->createNseDailyPrice('csv', '2013-4-20', 30.8, 11, 1000.0, 100.0, 0.6);
// var_dump($broker->someMarketReviewInfo('daily', 'pdf'));


// $TblDocument = new TblDocument();
// $items = [
//     ['WEMA', 'registrar form', 110],
//     ['FirstBank', 'client service form', 111],
//     ['OOP', 'public form', 112],
// ];
// foreach ($items as $keys => $item) {

//     $col = [TblDocument::NAME => [$item[0], 'isValue'], TblDocument::TYPE => [$item[1], 'isValue'], TblDocument::PRIORITY => [$item[2], 'isValue']];

//     var_dump($TblDocument->insert($col));
// }


// var_dump($TblDocument->select());

// $col = [TblDailyNews::BODY=>['stock exchange price has gain momemetum in the past days', 'isValue'], TblDailyNews::SOURCE=>['www.stockexchange.com.ng', 'isValue']];
// $where = [TblDailyNews::ID=>["=", 3   , 'isValue']];
// var_dump($TblDailyNews->update($col, $where)); 


// $where = [TblCorporateAction::ID=>["=", 3, 'isValue']];
// var_dump($TblCorporateAction->delete($where)); 












































//TblLogger::createTable();
//$TblLogger = new TblLogger();
//$TblLogger->populateTable(10);
//TblSession::createTable();
//TblProfileType::createTable();
//$TblProfileType = new TblProfileType();
//$TblProfileType->populateTable();
//TblProfile::createTable();
//TblStaff::createTable();
//TblGrade::createTable();
//TblWorker::createTable();
//TblSalaryComponent::createTable();
//TblGradeSalaryComponent::createTable();
//TblSalaryPymt::createTable();
//TblSalaryPymtApproval::createTable();
//TblQueuedSalaryPymt::createTable();
//TblArchivedSalaryPymt::createTable();
//var_dump(Staff::create("Alabi A.", "clerk", "password1", "alabi12@yahoo.com", TblLogger::EMAIL));
//var_dump(Worker::create("Clabi C.", "worker", "password1", "alabi15@yahoo.com", TblLogger::EMAIL, 1, TblWorker::TABLE, 1));
//var_dump(Worker::create("Dlabi D.", "worker", "password1", "alabi16@yahoo.com", TblLogger::EMAIL, 1, TblWorker::TABLE, 1));
//var_dump(Worker::create("Elabi E.", "worker", "password1", "alabi18@yahoo.com", TblLogger::EMAIL, 1, TblWorker::TABLE, 1));

//$TblWorker = new TblWorker();
//var_dump($TblWorker->updateById([TblWorker::GRADE_ID=>[2, 'isValue']], 1));

// $Salary = new Salary();
//var_dump($Salary->createGrade("Level 1", 1, "L09"));
//$Salary->editGrade(5, "Level 5", 2, "L05");
//$Salary->deleteGrade(6);
//var_dump($Salary->gradeInfo(1));
//var_dump($Salary->allGradeInfo());
//var_dump($Salary->createSalaryComponent("Special Bonus", 1, TblSalaryComponent::TYPE_VALUE[0]));
//$Salary->editSalaryComponent(2, "Transport Allowance", 2, TblSalaryComponent::TYPE_VALUE[1]);
//$Salary->deleteSalaryComponent(5);
//var_dump($Salary->salaryComponentInfo(2));
//var_dump($Salary->allSalaryComponentInfo(TblSalaryComponent::TYPE_VALUE[1], "", "active"));
//var_dump($Salary->addSalaryComponentToGrade(1, 4, 1, 5600, [1=>1.5]));
//var_dump($Salary->addSalaryComponentToGrade(2, 4, 1, 6600, [1=>2.5]));
//var_dump($Salary->editSalaryComponentToGrade(3, 2, 1, 1, 5000, [1=>2]));
//$Salary->deleteSalaryComponentToGrade(4);
//var_dump($Salary->salaryComponentToGradeInfo(1));
//var_dump($Salary->allSalaryComponentToGradeInfo(1, 2));

// $Worker = new Worker(13);
//$Worker->adjustSpecialSalaryComponent([4=>TblWorker::ADD, 1=>TblWorker::ADD, 2=>TblWorker::REMOVE]);

// $SalaryPymt  = new SalaryPymt();
//$SalaryPymt->createPymt("Mar'2023  Salary", 1, TblSalaryPymt::STYLE_VALUE[0], [2,3], [12, 13], TblSalaryComponent::FREQUENCY_VALUE[2]);
//$SalaryPymt->editPymt(30, "Feb'2023  Salary", TblSalaryPymt::STYLE_VALUE[0]);
//$SalaryPymt->deletePymt(30);
//var_dump($SalaryPymt->pymtInfo(31, false));
//var_dump($SalaryPymt->allPymtInfo(2, null));
//$SalaryPymt->additionalPymtApprovers(35, [3,1]);
//$SalaryPymt->removePymtApprover(35, 1);
//$SalaryPymt->approvePymt(31, 3, true);
//var_dump($SalaryPymt->pymtApprovalInfo(59));
//var_dump($SalaryPymt->allPymtApprovalInfo(0, 2, null));
//$SalaryPymt->addWorkersToPymtQueue(31, [16=>[1,2,3,4], 15=>[1,2,3,4]]);
//$SalaryPymt->adjustSalaryComponentOnPymtQueue(35, [15=>[1,2,3,4]]);
//$SalaryPymt->removeWorkersFrmPymtQueue(35, [16,15]);
//var_dump($SalaryPymt->pymtQueueInfo(37));
//var_dump($SalaryPymt->allPymtQueueInfo(0, 0, 3));
//$SalaryPymt->payItemInPymtQueue();
//var_dump($SalaryPymt->pymtArchiveInfo(2));
//var_dump($SalaryPymt->allPymtArchiveInfo(31, 0, false));

//TblAppOptions::createTable();
//(new TblAppOptions())->populateTable();
// $AppOptions = new AppOptions();
//echo $AppOptions->organisationName();
//$AppOptions->changeOrganisationName("Sample Company Limited");

//TblBank::createTable();
//(new TblBank())->populateTable();

//TblDeductionEntity::createTable();
//(new TblDeductionEntity())->populateTable();

//Bank::create("Alabian Bank", "B9090000");
//var_dump(Bank::allBankInfo());
//$Bank = new Bank(23);
//$Bank->edit("Tinubu Bank", "6788");
//$Bank->delete();
//var_dump($Bank->info());

// $accessBank =  new Bank("44150149", Bank::IDENTIFIER_VALUES[1]);
// var_dump($accessBank->info()[TblBank::ID]);


//TblWorkerDeductionEntity::createTable();
// (new TblWorkerDeductionEntity())->populateTable();

// TblBankTransaction::createTable();
// (new TblBankTransaction())->populateTable();

// TblDeductionEntityTransaction::createTable();
//(new TblDeductionEntityTransaction())->populateTable();

//TblArchivedWorkerDeductionEntity::createTable();

// var_dump(DeductionEntity::allDeductionEntityInfo());

//$SalaryPymt->payWorkersOnPymtQueue(41);

// TblPsReferenceNo::createTable();
// (new TblPsReferenceNo())->populateTable();

//TblPayStackPymt::createTable();
//(new TblPayStackPymt())->populateTable();
// var_dump((new Paystack())->verifyAccountNo('0798671743', '044'));
// var_dump((new Paystack())->checkPymtReferenceNo('D02K00006', '100000'));
// dump((new Paystack())->transfer('0798671743','044', 100000, 'test transfer'));

/* ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?= (new Paystack())->generatePayButton("alabi10@yahoo.com", 100000, "Deposit for staff salary march", "http://localhost/kallista/"); ?>

</body>
</html>
 */

/* TblWalletTransaction::createTable();
(new TblWalletTransaction())->populateTable();
 */

//$Wallet = new Wallet();
//var_dump($Wallet->statement("descending", new DateTime("2023-03-21")));
//var_dump($Wallet->balance(null, false));
/* $Wallet->credit(10000, "wallet credit testing");
$Wallet->debit(2000, "wallet debit testing");
var_dump($Wallet->balance()); */
// var_dump($Wallet = new WalletRemote());

// $a = [2,3,4];
// $b = [3,2,4];

// var_dump(array_diff($a, $b));

// var_dump( CronJobs::SalaryPayment());
// $Worker = new Worker(16);
// $workerInfo = $Worker->getInfo();
// dump($workerInfo);

// $Worker = new Bank(5);
// $workerInfo = $Worker->getSortCode();
// dump($workerInfo);

/* $salaryPymt = (new TblArchivedSalaryPymt())->selectById(20, [TblArchivedSalaryPymt::SALARY_PYMT_ID]);
$salaryPymt = (new TblSalaryPymt())->selectById($salaryPymt[0]['salary_pymt'], [TblSalaryPymt::NAME]); */

//var_dump((new Salary())->gradeSalaryComponentsCollection());

//$Wallet = new Wallet(true);
//$Wallet->credit(2000, "credit testing from app");
//var_dump($Wallet->statement());
//TblWalletTransaction::ACCOUNT_NO,TblWalletTransaction::AMOUNT, TblWalletTransaction::BALANCE,TblWalletTransaction::NARRATION, TblWalletTransaction::TRANSACTION_DATE
/* $data = [
    ['account_no'=>'34877491', 'amount'=>4000, 'narration'=>'remote debit 4', 'transaction_date'=>'2023-04-11'],
    ['account_no'=>'34877491', 'amount'=>5000, 'narration'=>'remote debit 5', 'transaction_date'=>'2023-04-11']];
var_dump($Wallet->bulkDebit($data));
 */

//var_dump(implode("", Functions::asciiCollection(8)));

//(new Salary)->editSalaryComponentToGrade(68, null, null, null, null, [], TblGradeSalaryComponent::STATUS_VALUE[1]);
