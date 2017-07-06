namespace php service.finance_lease_php
service FinanceLeaseService{
    /**
	 * 生成还款计划表
	 * @param creditNo 合同编号
	 * @param date 起租日
	 */
	string generateLoanPlan(1:string creditNo,2:i64 date );
	/**
	 * 测试生成贷款还款计划
	 * @param rule 1. 30/360 2. 31/365
	 * @param payMethod 1. 等额本金   2. 等额平息       3. 等本等息
	 * @param totalAmount 贷款总额
	 * @param ratio 利率
	 * @param timeLimit 期次
	 * @param sepicalDay 固定日期
	 */
	string testLoanPlan(1:i32 rule,2:i32 payMethod,3:double totalAmount,4:double ratio,5:i32 timeLimit,6:i32 sepicalDay );
	/**
	 * 核销费用
	 * @param feeId 费用ID
	 * @param flowId 流水ID
	 * @param factAmount 核销金额
	 * @param creatorId 邮箱
	 */
	string writeOffLoanFee(1:i32 feeId,2:i32 flowId,3:i32 factAmount,4:string creatorId);
	
		/**
	 * 还款计划表核销
	 * @param installmentId 还款计划表ID
	 * @param flowId 流水ID
	 * @param factAmount 核销金额
	 * @param creatorId 邮箱
	 * @return "1006" 核销成功
	 */
	string writeOffInstallment(1:i32 installmentId,2:i32 flowId,3:i32 factAmount,4:string creatorId);
	
	/**
	 * 还款计划表(逾期)核销
	 * @param installmentId 还款计划表ID
	 * @param flowId 流水ID
	 * @param factAmount 核销金额
	 * @param creatorId 邮箱
	 * @param derateAmount 减免金额
	 * @return "1006" 核销成功
	 */
	string writeOffInstallmentOverdue(1:i32 installmentId,2:i32 flowId,3:i32 factAmount,4:string creatorId,5:i32 derateAmount);
}