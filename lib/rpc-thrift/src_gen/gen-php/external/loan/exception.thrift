
//include

//namespace
namespace cpp external.loan.exception
namespace php external.loan.exception
namespace java external.loan.exception

exception BaseException
{
	1:	i32 retcode;
	2:	string retmsg;
	3:	i32 extcode;
}


/**
 * 2xxyyy - 表示server错误码<br/>
 * _xx___ - 表示server编号(暂定服务端口号后两位)<br/>
 * ___yyy - 表示具体错误码(分段定义如下)<br/>
 */
enum ERetCodeBase
{
	ERR_IN_USER_INFO_SERVER			= 279000,
	ERR_IN_CONFIG_SERVER			= 280000,
	ERR_IN_VERIFICATION_SERVER		= 281000,
	ERR_IN_ZLFUND_SERVER			= 282000,
	ERR_IN_MSG_AUDIT_SERVER			= 283000,
	ERR_IN_SM_SERVER				= 284000,
	ERR_IN_TA_PROC_SERVER			= 285000,
	ERR_IN_TRANSFER_SERVER			= 286000,
	ERR_IN_RECV_PAY_RESULT_SERVER	= 287000,
	ERR_IN_RECV_JINTONG_CALLBACK	= 288000,
	ERR_IN_CARD_AUTH_SERVER			= 289000,
	ERR_IN_ID_GEN_SERVER			= 290000,
	ERR_IN_LOG_SERVER				= 291000,
	ERR_IN_ORDER_SERVER				= 292000,
	ERR_IN_MT_SERVER				= 293000,
	ERR_IN_PAY_SERVER				= 294000,
	ERR_IN_TA_SYNC_SERVER			= 295000,
	ERR_IN_BATCH_PROCESS			= 296000,
	ERR_IN_PAY_CALLBACK_SERVER		= 297000,
	ERR_IN_PAY_REQ_SERVER			= 298000,
	ERR_IN_VACC_SERVER				= 299000,
}

