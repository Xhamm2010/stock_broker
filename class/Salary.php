<?php

/**
 * Salary
 *
 * A class managing salary
 * @author      Alabi A. <alabi.adebayo@alabiansolutions.com>
 * @copyright   Alabian Solutions Limited
 * @version 	1.0 => February 2023
 * @link        alabiansolutions.com
 */

class SalaryExpection extends Exception
{
}

class Salary
{
    /** @var DbConnect an instance of DbConnect  */
    protected DbConnect $dbConnect;

    /** @var Query an instance of Query  */
    protected Query $query;

    /** @var int value indicating all grade id was choosen*/
    public const ALL_GRADE_ID = -1;

    /** @var TblGrade an instance of TblGrade  */
    protected TblGrade $tblGrade;

    /** @var TblSalaryComponent an instance of TblSalaryComponent  */
    protected TblSalaryComponent $tblSalaryComponent;

    /** @var TblGradeSalaryComponent an instance of TblGradeSalaryComponent  */
    protected TblGradeSalaryComponent $tblGradeSalaryComponent;

    /** @var TblSalaryPymtTemplate an instance of TblSalaryPymtTemplate  */
    protected TblSalaryPymtTemplate $tblSalaryPymtTemplate;

    /**
     * instantiation of Salary
     *
     */
    public function __construct()
    {
        $DbConnect = DbConnect::getInstance(SETTING_FILE);
        $this->dbConnect = $DbConnect;
        $this->query = new Query();
        $this->tblGrade = new TblGrade();
        $this->tblSalaryComponent = new TblSalaryComponent();
        $this->tblGradeSalaryComponent = new TblGradeSalaryComponent();
        $this->tblSalaryPymtTemplate = new TblSalaryPymtTemplate();
    }

    /**
     * for creating a new grade
     *
     * @param string $name name of the grade
     * @param int $creator the profile id of the creator
     * @param string $code the code of the grade
     * @return int id of the newly created grade
     */
    public function createGrade(string $name, int $creator, string $code = ""): int
    {
        try {
            $cols = [TblGrade::NAME => [$name, 'isValue'], TblGrade::CREATOR => [$creator, 'isValue']];
            if ($code) {
                $cols[TblGrade::CODE] = [$code, 'isValue'];
            }
            $id = $this->tblGrade->insert($cols);
        } catch (Exception $e) {
            throw new SalaryExpection("Error creating grading: " . $e->getMessage());
        }
        return $id;
    }

    /**
     * for creating a new grade
     *
     * @param int $id the grade id
     * @param string $name name of the grade
     * @param int $creator the profile id of the creator
     * @param string $code the code of the grade
     * @return void
     */
    public function editGrade(int $id, string $name = "", int $creator = 0, string $code = "")
    {
        if (!$name && !$creator && !$code) {
            throw new SalaryExpection("either name or creator or code must be provided");
        }
        if ($name) {
            $cols[TblGrade::NAME] = [$name, 'isValue'];
        }
        if ($creator) {
            $cols[TblGrade::CREATOR] = [$creator, 'isValue'];
        }
        if ($code) {
            $cols[TblGrade::CODE] = [$code, 'isValue'];
        }
        $this->tblGrade->updateById($cols, $id);
    }

    /**
     * for deleting exiting grade that has no dependence
     *
     * @param int $id the grade id
     */
    public function deleteGrade(int $id)
    {
        try {
            $this->tblGrade->deleteById($id);
        } catch (Exception $e) {
            throw new SalaryExpection("this grade has dependence: " . $e->getMessage());
        }
    }

    /**
     * for getting info about a grade
     *
     * @param int $id the grade id
     * @param bool $addOtherInfo if true other information is added
     * @return array an array containing the grade information
     */
    public function gradeInfo(int $id, bool $addOtherInfo = true): array
    {
        $info = [];
        if ($info = $this->tblGrade->get($id)) {
            if ($addOtherInfo) {
                $Staff = new Staff($info[TblGrade::CREATOR]);
                $info[TblGrade::CREATOR . "Info"] = $Staff->getInfo();
            }
        }
        return $info;
    }

    /**
     * for getting info of all grade (all defaults to first 5,000 records)
     *
     * @param int $count the max no of grade to pull defaults to 5,000
     * @param int $creator profile id of the grade creator, if creator is skipped then is neglected in selection criteria
     * @param bool $addOtherInfo if true other information is added
     * @return array an array containing the grade information
     */
    public function allGradeInfo(int $count = 5000, int $creator = 0, bool $addOtherInfo = true): array
    {
        $info = $bind = [];
        $where = "";
        if ($creator) {
            $where = " WHERE " . TblGrade::CREATOR . " = :creator ";
            $bind = ['creator' => $creator];
        }
        $sql = "SELECT " . TblGrade::ID . " FROM " . TblGrade::TABLE . " $where ORDER BY " . TblGrade::ID . " DESC LIMIT $count";
        $result = $bind ? $this->query->executeSql($sql, $bind) : $this->query->executeSql($sql);
        if ($result['rows']) {
            foreach ($result['rows'] as $aResult) {
                $info[] = $this->gradeInfo($aResult[TblGrade::ID], $addOtherInfo);
            }
        }
        return $info;
    }

    /**
     * for creating salary component
     *
     * @param string $name the name of the salary component
     * @param int $creator profile id of the salary component creator
     * @param string $type the type of salary component if either increase or decrease
     * @param string $owner if salary component is owned by sys or profile
     * @param string $status the status of salary component if either active or inactive
     * @param string $frequency the frequency of payment for the salary component
     * @return int id of the newly created salary component
     */
    public function createSalaryComponent(
        string $name,
        int $creator,
        string $type,
        string $owner = TblSalaryComponent::OWNER_VALUE[1],
        string $status = TblSalaryComponent::STATUS_VALUE[0],
        string $frequency = TblSalaryComponent::FREQUENCY_VALUE[2]
    ): int {
        try {
            $cols = [
                TblSalaryComponent::NAME => [$name, 'isValue'], TblSalaryComponent::CREATOR => [$creator, 'isValue'],
                TblSalaryComponent::TYPE => [$type, 'isValue'], TblSalaryComponent::OWNER => [$owner, 'isValue'],
                TblSalaryComponent::STATUS => [$status, 'isValue'], TblSalaryComponent::FREQUENCY => [$frequency, 'isValue'],
            ];
            $id = $this->tblSalaryComponent->insert($cols);
        } catch (Exception $e) {
            throw new SalaryExpection("Error creating salary component: " . $e->getMessage());
        }
        return $id;
    }

    /**
     * for editing a salary component
     *
     * @param int $id the id of the salary component
     * @param string $name the name of the salary component
     * @param int $creator profile id of the salary component creator
     * @param string $type the type of salary component if either increase or decrease
     * @param string $owner if salary component is owned by sys or profile
     * @param string $status the status of salary component if either active or inactive
     * @param string $frequency the frequency of payment for the salary component
     * @return void
     */
    public function editSalaryComponent(
        int $id,
        string $name = "",
        int $creator = 0,
        string $type = "",
        string $owner = "",
        string $status = "",
        string $frequency = ""
    ) {
        if (!$name && !$creator && !$type && !$owner && !$status && !$frequency) {
            throw new SalaryExpection("either name, creator, owner, status or frequency must be provided");
        }
        if ($name) {
            $cols[TblSalaryComponent::NAME] = [$name, 'isValue'];
        }
        if ($creator) {
            $cols[TblSalaryComponent::CREATOR] = [$creator, 'isValue'];
        }
        if ($type) {
            $cols[TblSalaryComponent::TYPE] = [$type, 'isValue'];
        }
        if ($owner) {
            $cols[TblSalaryComponent::OWNER] = [$owner, 'isValue'];
        }
        if ($status) {
            $cols[TblSalaryComponent::STATUS] = [$status, 'isValue'];
        }
        if ($frequency) {
            $cols[TblSalaryComponent::FREQUENCY] = [$frequency, 'isValue'];
        }
        $this->tblSalaryComponent->updateById($cols, $id);
    }

    /**
     * for deleting existing salary component
     *
     * @param int $id the salary component
     */
    public function deleteSalaryComponent(int $id)
    {
        try {
            $this->tblSalaryComponent->deleteById($id);
        } catch (Exception $e) {
            throw new SalaryExpection("this salary component has dependence: " . $e->getMessage());
        }
    }

    /**
     * for getting info about a salary component
     *
     * @param int $id the salary component id
     * @param bool $addOtherInfo if true other information is added
     * @return array an array containing the salary component information
     */
    public function salaryComponentInfo(int $id, bool $addOtherInfo = true): array
    {
        $info = [];
        if ($info = $this->tblSalaryComponent->get($id)) {
            if ($addOtherInfo) {
                $Staff = new Staff($info[TblGrade::CREATOR]);
                $info[TblSalaryComponent::CREATOR . "Info"] = $Staff->getInfo();
            }
        }
        return $info;
    }

    /**
     * for getting info of all salary component (all defaults to first 5,000 records)
     *
     * @param string $type the type of salary component if either increase or decrease
     * @param string $owner if salary component is owned by sys or profile
     * @param string $status the status of salary component if either active or inactive
     * @param string $frequency the frequency of payment for the salary component
     * @param int $count the max no of salary component to pull defaults to 5,000
     * @param int $creator profile id of the salary component creator, if creator is skipped then is neglected in selection criteria
     * @param bool $addOtherInfo if true other information is added
     * @return array an array containing the salary component information
     */
    public function allSalaryComponentInfo(
        string $type = "",
        string $owner = "",
        string $status = "",
        string $frequency = "",
        int $count = 5000,
        int $creator = 0,
        bool $addOtherInfo = true
    ): array {
        $info = $bind = [];
        $where = "";
        if ($creator) {
            $where .= " WHERE " . TblSalaryComponent::CREATOR . " = :creator ";
            $bind['creator'] = $creator;
        }
        if ($type) {
            $where .= $where ?  " AND " . TblSalaryComponent::TYPE . " = :type " : " WHERE " . TblSalaryComponent::TYPE . " = :type ";
            $bind['type'] = $type;
        }
        if ($owner) {
            $where .= $where ?  " AND " . TblSalaryComponent::OWNER . " = :owner " : " WHERE " . TblSalaryComponent::OWNER . " = :owner ";
            $bind['owner'] = $owner;
        }
        if ($status) {
            $where .= $where ?  " AND " . TblSalaryComponent::STATUS . " = :status " : " WHERE " . TblSalaryComponent::STATUS . " = :status ";
            $bind['status'] = $status;
        }
        if ($frequency) {
            $where .= $where ?  " AND " . TblSalaryComponent::FREQUENCY . " = :frequency " : " WHERE " . TblSalaryComponent::FREQUENCY . " = :frequency ";
            $bind['frequency'] = $frequency;
        }
        $sql = "SELECT " . TblSalaryComponent::ID . " FROM " . TblSalaryComponent::TABLE . " $where ORDER BY " . TblSalaryComponent::ID . " DESC LIMIT $count";
        $result = $bind ? $this->query->executeSql($sql, $bind) : $this->query->executeSql($sql);
        if ($result['rows']) {
            foreach ($result['rows'] as $aResult) {
                $info[] = $this->salaryComponentInfo($aResult[TblSalaryComponent::ID], $addOtherInfo);
            }
        }
        return $info;
    }

    /**
     * for adding salary component grade
     *
     * @param int $gradeId the grade id
     * @param int $salaryComponentId the salary component id
     * @param int $creator profile id of the profile adding the salary component to grade
     * @param int $amount the amount for the grade for this salary component
     * @param string $percent collection of percent of salary components [scIndex=>percent,...]
     * @return int id of the newly created row representing grade to salary component addition
     */
    public function addSalaryComponentToGrade(int $gradeId, int $salaryComponentId, int $creator, float $amount = 0, array $percent = []): int
    {
        try {
            if ($percent) {
                foreach ($percent as $scIndex => $aPercent) {
                    if ($scIndex == $salaryComponentId) {
                        throw new SalaryExpection("salary component same percentage of itself");
                    }
                }
            }
            $cols = [
                TblGradeSalaryComponent::GRADE_ID => [$gradeId, 'isValue'],
                TblGradeSalaryComponent::SALARY_COMPONENT_ID => [$salaryComponentId, 'isValue'],
                TblGradeSalaryComponent::CREATOR => [$creator, 'isValue']
            ];
            if ($amount) {
                $cols[TblGradeSalaryComponent::AMOUNT] = [$amount, 'isValue'];
            }
            if ($percent) {
                $cols[TblGradeSalaryComponent::PERCENT] = [$percent, 'isValue'];
            }
            $id = $this->tblGradeSalaryComponent->insert($cols);
        } catch (Exception $e) {
            throw new SalaryExpection("Error creating grade salary component: " . $e->getMessage());
        }
        return $id;
    }

    /**
     * for editing a salary component to grade
     *
     * @param int $id the id of the salary component to grade
     * @param int|null $gradeId the grade id
     * @param int|null $salaryComponentId the salary component id
     * @param int|null $creator profile id of the profile adding the salary component to grade
     * @param string|null $amount the amount for the grade for this salary component
     * @param string|null $percent collection of percent of salary components [scIndex=>percent,...]
     * @return void
     */
    public function editSalaryComponentToGrade(
        int $id,
        int|null $gradeId = null,
        int|null $salaryComponentId = null,
        int|null $creator = null,
        float|null $amount = null,
        array|null $percent = null,
        string|null $status = null
    ) {
        $errors  = [];
        if (!$gradeId && !$salaryComponentId && !$creator && !$amount && !$percent && !$status) {
            $errors[] = "either grade id, salary component id, creator, amount, percent must be provided";
        }

        $where = [
            TblGradeSalaryComponent::SALARY_COMPONENT_ID => ['=', $salaryComponentId, 'isValue', 'AND'],
            TblGradeSalaryComponent::GRADE_ID => ['=', $gradeId, 'isValue']
        ];
        if ($this->tblGradeSalaryComponent->select([TblGradeSalaryComponent::ID], $where)) {
            $errors[] = "editing will result in duplication of grade id and salary component id";
        }

        if ($errors) {
            throw new SalaryExpection("Salary Component to Grade Editing error: " . implode(",", $errors));
        }

        if ($gradeId !== null) {
            $cols[TblGradeSalaryComponent::GRADE_ID] = [$gradeId, 'isValue'];
        }
        if ($salaryComponentId !== null) {
            $cols[TblGradeSalaryComponent::SALARY_COMPONENT_ID] = [$salaryComponentId, 'isValue'];
        }
        if ($creator !== null) {
            $cols[TblGradeSalaryComponent::CREATOR] = [$creator, 'isValue'];
        }
        if ($amount !== null) {
            $cols[TblGradeSalaryComponent::AMOUNT] = [$amount, 'isValue'];
        }
        if ($percent !== null) {
            $cols[TblGradeSalaryComponent::PERCENT] = [$percent, 'isValue'];
        }
        if ($status !== null) {
            $cols[TblGradeSalaryComponent::STATUS] = [$status, 'isValue'];
        }
        
        $this->tblGradeSalaryComponent->updateById($cols, $id);
    }

    /**
     * for deleting existing grade salary component
     *
     * @param int $id the grade salary component id
     */
    public function deleteSalaryComponentToGrade(int $id)
    {
        try {
            $this->tblGradeSalaryComponent->deleteById($id);
        } catch (Exception $e) {
            throw new SalaryExpection("this grade salary component has dependence: " . $e->getMessage());
        }
    }

    /**
     * check if a particular grade has a salary component, if status is not empty then check will be; grade to salary component with the supplied status
     *
     * @param int $gradeId the grade id
     * @param int $salaryComponentId the salary component id
     * @param string $status grade to salary component status
     * @return bool true if the grade has a salary component, false otherwise
     */
    public function isSalaryComponentInGrade(int $gradeId, int $salaryComponentId, string $status = ''): bool
    {
        $scIsInGrade = false;
        if ($status) {
            $where[TblGradeSalaryComponent::STATUS] = ['=', $status, 'isValue', 'AND'];
        }
        $where[TblGradeSalaryComponent::GRADE_ID] = ['=', $gradeId, 'isValue', 'AND'];
        $where[TblGradeSalaryComponent::SALARY_COMPONENT_ID] = ['=', $salaryComponentId, 'isValue'];
        if ($this->tblGradeSalaryComponent->select([TblGradeSalaryComponent::ID], $where)) {
            $scIsInGrade = true;
        }
        return $scIsInGrade;
    }

    /**
     * for getting info about a grade salary component
     *
     * @param int $id the grade salary component id
     * @param bool $addOtherInfo if true other information is added
     * @return array an array containing the grade salary component information
     */
    public function salaryComponentToGradeInfo(int $id, bool $addOtherInfo = true): array
    {
        $info = [];
        if ($info = $this->tblGradeSalaryComponent->get($id)) {
            if ($addOtherInfo) {
                $Staff = new Staff($info[TblGrade::CREATOR]);
                $info[TblGradeSalaryComponent::GRADE_ID . "Info"] = (new TblGrade())->get($info[TblGrade::GRADE_ID]);
                $info[TblGradeSalaryComponent::SALARY_COMPONENT_ID . "Info"] = (new TblSalaryComponent())->get($info[TblGrade::SALARY_COMPONENT_ID]);
                $info[TblGradeSalaryComponent::CREATOR . "Info"] = $Staff->getInfo();
            }
        }
        return $info;
    }

    /**
     * for getting info of all grade salary component (all defaults to first 5,000 records)
     *
     * @param int $gradeId the grade id
     * @param int $salaryComponentId the salary component id
     * @param int $count the max no of salary component to pull defaults to 5,000
     * @param int $creator profile id of the salary component creator, if creator is skipped then is neglected in selection criteria
     * @param bool $addOtherInfo if true other information is added
     * @return array an array containing the grade salary component information
     */
    public function allSalaryComponentToGradeInfo(
        int $gradeId = 0,
        int $salaryComponentId = 0,
        string $status = '',
        int $count = 5000,
        int $creator = 0,
        bool $addOtherInfo = true
    ): array {
        $info = $bind = [];
        $where = "";
        if ($creator) {
            $where .= " WHERE " . TblGradeSalaryComponent::CREATOR . " = :creator ";
            $bind['creator'] = $creator;
        }
        if ($gradeId) {
            $where .= $where ?  " AND " . TblGradeSalaryComponent::GRADE_ID . " = :grade " : " WHERE " . TblGradeSalaryComponent::GRADE_ID . " = :grade ";
            $bind['grade'] = $gradeId;
        }
        if ($salaryComponentId) {
            $where .= $where ?  " AND " . TblGradeSalaryComponent::SALARY_COMPONENT_ID . " = :salaryComponent " :
                " WHERE " . TblGradeSalaryComponent::SALARY_COMPONENT_ID . " = :salaryComponent ";
            $bind['salaryComponent'] = $salaryComponentId;
        }
        if ($status) {
            $where .= $where ?  " AND " . TblGradeSalaryComponent::STATUS . " = :status " : " WHERE " . TblGradeSalaryComponent::STATUS . " = :status ";
            $bind['status'] = $status;
        }
        $sql = "SELECT " . TblGradeSalaryComponent::ID . " FROM " . TblGradeSalaryComponent::TABLE . " $where ORDER BY " . TblGradeSalaryComponent::ID . " DESC LIMIT $count";
        $result = $bind ? $this->query->executeSql($sql, $bind) : $this->query->executeSql($sql);
        if ($result['rows']) {
            foreach ($result['rows'] as $aResult) {
                $info[] = $this->salaryComponentToGradeInfo($aResult[TblGradeSalaryComponent::ID], $addOtherInfo);
            }
        }
        return $info;
    }

    /**
     * for getting info of all grade salary component per grade
     *
     * @return array an array containing the grade salary component information for each grade
     */
    public function gradeInfoWithSalaryComponents(): array
    {
        $gradeSalaryComp  = [];
        $SalaryComponentGrade = $this->allSalaryComponentToGradeInfo();
        if ($SalaryComponentGrade) {
            foreach ($SalaryComponentGrade as $info) {
                $gradeInfo = $info['gradeInfo'];
                $gradeSalaryComp[$info["grade"]]['gradeInfo'] = $info['gradeInfo'];
                $gradeSalaryComp[$info["grade"]]['salary_components'][$info['salary_componentInfo']['id']] = $info['salary_componentInfo'];
            }
        }
        return $gradeSalaryComp;
    }

    /**
     * for getting an array of all grades and their salary components info
     *
     * @return array the array of grade and salary component
     */
    public function gradeSalaryComponentsCollection(): array
    {
        $gradeCollection  = $gradeAmtCollection = [];
        $gscTbl = new TblGradeSalaryComponent();
        $gTbl = new TblGrade();
        $scTbl = new TblSalaryComponent();
        $sql = "SELECT ".$gscTbl::TABLE.".".$gscTbl::GRADE_ID.", ".$gTbl::TABLE.".".$gTbl::NAME." as gradeName, ".$gscTbl::TABLE.".".$gscTbl::SALARY_COMPONENT_ID.", 
                ".$scTbl::TABLE.".".$scTbl::NAME." as scName, ".$gscTbl::TABLE.".".$gscTbl::AMOUNT.", ".$gscTbl::TABLE.".".$gscTbl::PERCENT.", 
                ".$gscTbl::TABLE.".".$gscTbl::STATUS.", ".$scTbl::TABLE.".".$scTbl::TYPE."  
            FROM ".$gscTbl::TABLE."
                INNER JOIN ".$gTbl::TABLE." ON ".$gTbl::TABLE.".".$gTbl::ID." = ".$gscTbl::TABLE.".".$gscTbl::GRADE_ID."
                INNER JOIN ".$scTbl::TABLE." ON ".$scTbl::TABLE.".".$scTbl::ID." = ".$gscTbl::TABLE.".".$gscTbl::SALARY_COMPONENT_ID."";
        $Query = new Query();
        if($result = $Query->executeSql($sql)['rows']) {
            foreach($result as $aResult) {
                if(isset($gradeAmtCollection[$aResult[$gscTbl::GRADE_ID]])) {
                    $gradeAmtCollection[$aResult[$gscTbl::GRADE_ID]][$aResult[$gscTbl::SALARY_COMPONENT_ID]] = [
                        "amount"=> $aResult[$gscTbl::AMOUNT]
                    ];
                } else {
                    $gradeAmtCollection[$aResult[$gscTbl::GRADE_ID]][$aResult[$gscTbl::SALARY_COMPONENT_ID]] =[
                        "amount"=> $aResult[$gscTbl::AMOUNT],
                    ];
                }
            }
        }
        
        if($result = $Query->executeSql($sql)['rows']) {
            foreach($result as $aResult) {
                $computeAmount = $aResult[$gscTbl::AMOUNT];
                if(isset($gradeCollection[$aResult[$gscTbl::GRADE_ID]])) {
                    if($aResult[$gscTbl::PERCENT]) {
                        foreach(json_decode($aResult[$gscTbl::PERCENT], true) as $scIndex => $scPercent) {
                            $computeAmount += ($gradeAmtCollection[$aResult[$gscTbl::GRADE_ID]][$scIndex]['amount'] * 0.01 * $scPercent);
                        }
                    }
                    $gradeCollection[$aResult[$gscTbl::GRADE_ID]][$aResult[$gscTbl::SALARY_COMPONENT_ID]] = [
                        "amount"=> $aResult[$gscTbl::AMOUNT],
                        "percent"=> $aResult[$gscTbl::PERCENT],
                        "computeAmount"=> (float) $computeAmount,
                        "name"=> $aResult['scName'],
                        "status"=> $aResult[$gscTbl::STATUS],
                        "type"=> $aResult[$scTbl::TYPE],
                    ];
                } else {
                    if($aResult[$gscTbl::PERCENT]) {
                        foreach(json_decode($aResult[$gscTbl::PERCENT], true) as $scIndex => $scPercent) {
                            $computeAmount += ($gradeAmtCollection[$aResult[$gscTbl::GRADE_ID]][$scIndex]['amount'] * 0.01 * $scPercent);
                        }
                    }
                    $gradeCollection[$aResult[$gscTbl::GRADE_ID]][$aResult[$gscTbl::SALARY_COMPONENT_ID]] =[
                        "amount"=> $aResult[$gscTbl::AMOUNT],
                        "percent"=> $aResult[$gscTbl::PERCENT],
                        "computeAmount"=> (float) $computeAmount,
                        "name"=> $aResult['scName'],
                        "status"=> $aResult[$gscTbl::STATUS],
                        "type"=> $aResult[$scTbl::TYPE],
                    ];
                }
            }
        }
        return $gradeCollection;
    }

    /**
     * for creating a new salary pymt template
     *
     * @param string $name name of the salary created from this salary pymt template
     * @param string $period period the period of payment
     * @param array $approvers a collection of profile ids of staff to approve the salary payment created of this salary template
     * @param array $grades a collection of grades ids of staff's grade, pass empty array if template is for per worker
     * @param array $salaryComponents a collection of salary component
     * @param int $creator the profile id of the creator
     * @param string $transfer the transfer style either automate, manual or ineligible
     * @param string $style the approval order either consecutively or concurrently
     * @return int id of the newly created salary pymt template
     */
    public function createSalaryPymtTemplate(
        string $name,
        string $period,
        array $approvers,
        array $grades,
        array $salaryComponents,
        int $creator,
        string $transfer = TblSalaryPymtTemplate::TRANSFER_VALUE[0],
        string $style = TblSalaryPymtTemplate::STYLE_VALUE[0]
    ): int {
        try {
            $cols = [TblSalaryPymtTemplate::NAME => [$name, 'isValue'],  TblSalaryPymtTemplate::PERIOD => [$period, 'isValue'],
                TblSalaryPymtTemplate::APPROVERS => [$approvers, 'isValue'], TblSalaryPymtTemplate::CREATOR => [$creator, 'isValue'],
                TblSalaryPymtTemplate::TRANSFER => [$transfer, 'isValue'], TblSalaryPymtTemplate::STYLE => [$style, 'isValue']
            ];
            if($grades) {
                $cols[TblSalaryPymtTemplate::GRADES] = [$grades, 'isValue'];
                $cols[TblSalaryPymtTemplate::SALARY_COMPONENTS] = [$salaryComponents, 'isValue'];
            } else {
                $cols[TblSalaryPymtTemplate::WORKER_SC_AMT] = [$salaryComponents, 'isValue'];
            }
            $id = $this->tblSalaryPymtTemplate->insert($cols);
        } catch (Exception $e) {
            throw new SalaryExpection("Error creating salary pymt template: " . $e->getMessage());
        }
        return $id;
    }

    /**
     * for editing a salary pymt template
     *
     * @param int $id the salary pymt template id
     * @param string $period period the period of payment
     * @param array $approvers a collection of profile ids of staff to approve the salary payment created of this salary template
     * @param array $grades a collection of grades ids of staff's grade
     * @param array $salaryComponents a collection of salary component
     * @param int $creator the profile id of the creator
     * @param string $transfer the transfer style either automate, manual or ineligible
     * @param string $style the approval order either consecutively or concurrently
     * @return void
     */
    public function editSalaryPymtTemplate(
        int $id,
        string $name = "",
        string $period = "",
        array $approvers = [],
        array $grades = [],
        array $salaryComponents = [],
        int $creator = 0,
        string $transfer = "",
        string $style = ""
    ) {
        if ($name) {
            $cols[TblSalaryPymtTemplate::NAME] = [$name, 'isValue'];
        }
        if ($period) {
            $cols[TblSalaryPymtTemplate::PERIOD] = [$period, 'isValue'];
        }
        if ($approvers) {
            $cols[TblSalaryPymtTemplate::APPROVERS] = [$approvers, 'isValue'];
        }
        if ($grades) {
            $cols[TblSalaryPymtTemplate::GRADES] = [$grades, 'isValue'];
        }
        if ($salaryComponents) {
            $cols[TblSalaryPymtTemplate::SALARY_COMPONENTS] = [$salaryComponents, 'isValue'];
        }
        if ($creator) {
            $cols[TblSalaryPymtTemplate::CREATOR] = [$creator, 'isValue'];
        }
        if ($transfer) {
            $cols[TblSalaryPymtTemplate::TRANSFER] = [$transfer, 'isValue'];
        }
        if ($style) {
            $cols[TblSalaryPymtTemplate::STYLE] = [$style, 'isValue'];
        }
        $this->tblSalaryPymtTemplate->updateById($cols, $id);
    }

    /**
     * for deleting exiting salary pymt template that has no dependence
     *
     * @param int $id the salary pymt template id
     */
    public function deleteSalaryPymtTemplate(int $id)
    {
        try {
            $this->tblSalaryPymtTemplate->deleteById($id);
        } catch (Exception $e) {
            throw new SalaryExpection("this salary pymt template has dependence: " . $e->getMessage());
        }
    }

    /**
     * for getting info about a salary pymt template
     *
     * @param int $id the salary pymt template id
     * @param bool $addOtherInfo if true other information is added
     * @return array an array containing the salary pymt template information
     */
    public function salaryPymtTemplateInfo(int $id, bool $addOtherInfo = true): array
    {
        $info = [];
        if ($info = $this->tblSalaryPymtTemplate->get($id)) {
            if ($addOtherInfo) {
                $staffInfo = [];
                $where = [TblProfile::PROFILE_TYPE => ["=", (new ProfileTypeMgr)->getAllProfileTypeIds()[TblStaff::TABLE], 'isValue']];
                if($resultStaff = (new TblProfile)->select([], $where)) {
                    foreach($resultStaff as $aResultStaff) {
                        $staffInfo[$aResultStaff[TblProfile::ID]] = $aResultStaff;
                    }
                }
                $approvers = [];
                foreach(json_decode($info[TblSalaryPymtTemplate::APPROVERS], true) as $anApprover) {
                    $approvers[] = $staffInfo[$anApprover];
                }
                
                $grades = [];
                if($info[TblSalaryPymtTemplate::GRADES]) {
                    $gradeInfo = [];
                    if($resultGrade = (new TblGrade)->select()) {
                        foreach($resultGrade as $aResultGrade) {
                            $gradeInfo[$aResultGrade[TblGrade::ID]] = $aResultGrade;
                        }
                    }

                    foreach(json_decode($info[TblSalaryPymtTemplate::GRADES], true) as $aGrade) {
                        $grades[$aGrade] = $gradeInfo[$aGrade];
                    }
                }
                
                $scs = [];
                if($info[TblSalaryPymtTemplate::SALARY_COMPONENTS]) {
                    $scsInfo = [];
                    if($resultSc = (new TblSalaryComponent)->select()) {
                        foreach($resultSc as $aResultSc) {
                            $scsInfo[$aResultSc[TblSalaryComponent::ID]] = $aResultSc;
                        }
                    }
                
                    foreach(json_decode($info[TblSalaryPymtTemplate::SALARY_COMPONENTS], true) as $aScs) {
                        $scs[$aScs] = $scsInfo[$aScs];
                    }
                }

                $info[TblSalaryPymtTemplate::APPROVERS . "Info"] = $approvers;
                $info[TblSalaryPymtTemplate::GRADES . "Info"] = $grades;
                $info[TblSalaryPymtTemplate::SALARY_COMPONENTS . "Info"] = $scs;
                $info[TblSalaryPymtTemplate::CREATOR . "Info"] = $staffInfo[$info[TblSalaryPymtTemplate::CREATOR]];
            }
        }
        return $info;
    }

    /**
     * for getting info of all salary pymt template (all defaults to first 5,000 records)
     *
     * @param int $count the max no of salary pymt template to pull defaults to 5,000
     * @param int $creator profile id of the salary pymt template creator, if creator is skipped then is neglected in selection criteria
     * @param bool $addOtherInfo if true other information is added
     * @return array an array containing the salary pymt template information
     */
    public function allSalaryPymtTemplateInfo(int $count = 5000, int $creator = 0, bool $addOtherInfo = true): array
    {
        $info = $bind = [];
        $where = "";
        if ($creator) {
            $where = " WHERE " . TblSalaryPymtTemplate::CREATOR . " = :creator ";
            $bind = ['creator' => $creator];
        }
        $sql = "SELECT * FROM " . TblSalaryPymtTemplate::TABLE . " $where ORDER BY " . TblSalaryPymtTemplate::ID . " DESC LIMIT $count";
        $result = $bind ? $this->query->executeSql($sql, $bind) : $this->query->executeSql($sql);
        if ($result['rows']) {
            if ($addOtherInfo) {
                $staffInfo = [];
                $where = [TblProfile::PROFILE_TYPE => ["=", (new ProfileTypeMgr)->getAllProfileTypeIds()[TblStaff::TABLE], 'isValue']];
                if($resultStaff = (new TblProfile)->select([], $where)) {
                    foreach($resultStaff as $aResultStaff) {
                        $staffInfo[$aResultStaff[TblProfile::ID]] = $aResultStaff;
                    }
                }

                $gradeInfo = [];
                if($resultGrade = (new TblGrade)->select()) {
                    foreach($resultGrade as $aResultGrade) {
                        $gradeInfo[$aResultGrade[TblGrade::ID]] = $aResultGrade;
                    }
                }

                $scsInfo = [];
                if($resultSc = (new TblSalaryComponent)->select()) {
                    foreach($resultSc as $aResultSc) {
                        $scsInfo[$aResultSc[TblSalaryComponent::ID]] = $aResultSc;
                    }
                }
            }

            foreach ($result['rows'] as $aResult) {
                if($addOtherInfo) {
                    $approvers = [];
                    foreach(json_decode($aResult[TblSalaryPymtTemplate::APPROVERS], true) as $anApprover) {
                        $approvers[$anApprover] = $staffInfo[$anApprover];
                    }
                    $aResult[TblSalaryPymtTemplate::APPROVERS."Info"] = $approvers;
                    $grades = [];
                    if($aResult[TblSalaryPymtTemplate::GRADES]) {
                        foreach(json_decode($aResult[TblSalaryPymtTemplate::GRADES], true) as $aGrade) {
                            $grades[$aGrade] = $gradeInfo[$aGrade];
                        }
                    }
                    $aResult[TblSalaryPymtTemplate::GRADES."Info"] = $grades;
                    $scs = [];
                    if($aResult[TblSalaryPymtTemplate::SALARY_COMPONENTS]) {
                        foreach(json_decode($aResult[TblSalaryPymtTemplate::SALARY_COMPONENTS], true) as $aScs) {
                            $scs[$aScs] = $scsInfo[$aScs];
                        }
                    }
                    $aResult[TblSalaryPymtTemplate::SALARY_COMPONENTS."Info"] = $scs;
                    $aResult[TblSalaryPymtTemplate::CREATOR."Info"] = $staffInfo[$aResult[TblSalaryPymtTemplate::CREATOR]];
                }
                $info[] = $aResult;
            }
        }
        return $info;
    }
}
