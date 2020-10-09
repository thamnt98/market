<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Sprint\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;

use \Trans\Sprint\Api\Data\BankInterface;
use \Trans\Sprint\Api\Data\BankBinInterface;
use \Trans\Sprint\Model\BankFactory;
use \Trans\Sprint\Model\BankBinFactory;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var QuoteSetupFactory
     */
    protected $quoteSetupFactory;

    /**
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var BankFactory
     */
    protected $bankFactory;

    /**
     * @var BankBinFactory
     */
    protected $bankBinFactory;

    

    /**
     * Constructor
     */
    public function __construct(
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory,
        EavSetupFactory $eavSetupFactory,
        BankFactory $bankFactory,
        BankBinFactory $bankBinFactory
    ) {
        $this->quoteSetupFactory        = $quoteSetupFactory;
        $this->salesSetupFactory        = $salesSetupFactory;
        $this->eavSetupFactory          = $eavSetupFactory;
        $this->bankFactory              = $bankFactory;
        $this->bankBinFactory           = $bankBinFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.3', '<')) {

            /** @var \Magento\Quote\Setup\QuoteSetup $quoteInstaller */
            $quoteInstaller = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);

            /** @var \Magento\Sales\Setup\SalesSetup $salesInstaller */
            $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);

            //Add attributes to quote
            $entityAttributes = [
            'klikbca_userid' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            ];

            foreach ($entityAttributes as $code => $type) {
                $quoteInstaller->addAttribute('quote', $code, ['type' => $type, 'length' => 255, 'visible' => true, 'nullable' => true,]);
                $salesInstaller->addAttribute('order', $code, ['type' => $type, 'length' => 255, 'visible' => true, 'nullable' => true,]);
            }
        }

        if (version_compare($context->getVersion(), '1.0.4', '<')) {

            /** @var \Magento\Quote\Setup\QuoteSetup $quoteInstaller */
            $quoteInstaller = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);

            /** @var \Magento\Sales\Setup\SalesSetup $salesInstaller */
            $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);

            //Add attributes to quote
            $entityAttributes = [
            'sprint_term_channelid' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            ];

            foreach ($entityAttributes as $code => $type) {
                $quoteInstaller->addAttribute('quote', $code, ['type' => $type, 'length' => 255, 'visible' => true, 'nullable' => true,]);
                $salesInstaller->addAttribute('order', $code, ['type' => $type, 'length' => 255, 'visible' => true, 'nullable' => true,]);
            }
        }

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->installBankData();
        }
        
        $setup->endSetup();
    }

     /**
     * Install Bank Data
     */
    protected function installBankData(){

        $bankData= [
            [
                BankInterface::NAME         =>'Bank Central Asia (BCA)',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_DB =>'537941,526051,513458',
                    BankBinInterface::BIN_TYPE_CC =>'379565,445377,447242,455633,469150,455632,469151,472646,472647,477377,483545,515291,522990,540912,541322,542643,543248,469149,484073,431657,530456,356281,356280',
                    BankBinInterface::BIN_TYPE_DBCC =>'469150'
                ]
            ],
            [
                BankInterface::NAME         =>'Bank Mandiri',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_DB =>'603298,405651,409766,416232,421195,461699,461700,483795,483796,486682',
                    BankBinInterface::BIN_TYPE_CC =>'356350,400376,400378,400385,400479,400481,413718,413719,414931,415016,415018,415030,415031,415032,416230,416231,421197,425945,434075,445076,450183,450184,450185,461662,468748,468749,479929,489594,489764,490283,490284,512676,512724,524325,537793 ,550001,557338,415017,421313,479930,479931,437527,437528,437529'
                ]
            ],
            [
                BankInterface::NAME         =>'Bank Rakyat Indonesia (BRI)',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_DB =>'510458,522184,532659,524654',
                    BankBinInterface::BIN_TYPE_CC =>'356510,435965,435972,436502,468740,518828,518856,535835,547582,552002,553479,408968,472613,481400'
                ]
                
            ],
            [
                BankInterface::NAME         =>'Bank CIMB Niaga',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_DB =>'421405,426535,476085,517417,519629,521102,528674,529620,532713,536612,536788,537656,547292,557692,524818,530487,537970,543897,537689,524671',
                    BankBinInterface::BIN_TYPE_CC =>'428416,441659,443172,456878,456879,457941,457942,459920,459921,467908,489503,522866,524319,528919,533619,540174,540378,540462,540468,542441,542466,546318,548116,548117,552810,540469,356534,356536,519299,525799,516494,356535'
                ]
            ],
            [
                BankInterface::NAME         =>'Bank MNC Internasional',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_DB =>'511875',
                    BankBinInterface::BIN_TYPE_CC =>'426750,432442,432443,512630'
                ]
                
            ],
            [
                BankInterface::NAME         =>'Bank Mega',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_DB =>'421407,421408',
                    BankBinInterface::BIN_TYPE_CC =>'420191,420192,420194,431226,458785,464933,472670,478487,489087,524261,515874,522103,548495,552378',
                    BankBinInterface::BIN_TYPE_DBCC =>'420193,426211'
                ]
            ],
            [
                BankInterface::NAME         =>'Bank Bukopin',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_DB =>'409873,461750,461751,464790,473189,516055,532595,517415',
                    BankBinInterface::BIN_TYPE_CC =>'408862,421167,421168,489781,523940,526853,552695,601378'
                ]
            ],
            [
                BankInterface::NAME         =>'Bank Danamon Indonesia',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_DB =>'434098,451285,510462,523983,529984,557791,589587',
                    BankBinInterface::BIN_TYPE_CC =>'402335,425857,432449,434099,439040,451286,452485,456798,456799,490295,516634,520191,521343,521558,524064,527460,540731,542260,543415,546592,548198,552239,552338,552884,557790,559228,375532,405516,450722,455770,490296,496698,520166,521896,527461,542181,542651,546593,548199,375531,375539'
                ]
            ],
            [
                BankInterface::NAME         =>'Bank DBS Indonesia',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_DB =>'430980,437728,460238',
                    BankBinInterface::BIN_TYPE_CC =>'405542,415735,415736,416335,430981,437450,437451,437456,437714,437715,437726,437734,458769,463722,510217,510249,512021,512422,522846,524101,525644,528912,541069,541070,541616,531861,534142,540235,422143,418154'
                ]
            ],
            [
                BankInterface::NAME         =>'PT Aeon Credit Indonesia',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_CC =>'452056,111111'
                ]
            ],
            [
                BankInterface::NAME         =>'Bank Maybank Indonesia',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_DB =>'493828,531810,552080',
                    BankBinInterface::BIN_TYPE_CC =>'404776,405577,426013,442373,442374,56781,464987,493829,510481,515595,520037,540160,542449,545280,545298,545299,552008,356285'
                ]
            ],
            [
                BankInterface::NAME         =>'Bank Mayapada',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_CC =>'420663,420657,420653'
                ]
                
            ],
            [
                BankInterface::NAME         =>'Bank OCBC NISP',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_DB =>'464584,464585,486399,603439,518316',
                    BankBinInterface::BIN_TYPE_CC =>'421561,464583,469148,524169,345678,498766',
                    BankBinInterface::BIN_TYPE_DBCC =>'498765'
                ]
            ],
            [
                BankInterface::NAME         =>'Panin Bank',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_DB =>'526414',
                    BankBinInterface::BIN_TYPE_CC =>'436432,437700,437701,450246,557795'
                ]
            ],
            [
                BankInterface::NAME         =>'Bank ANZ Indonesia',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_CC =>'437703,437727,437716'
                ]
            ],
            [
                BankInterface::NAME         =>'Bank Permata',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_DB =>'426254,464005,471295,489385,417295,589385,476334,426454',
                    BankBinInterface::BIN_TYPE_CC =>'429750,454633,461785,498853,518943,520383,528872,540889,540890,,542167,543972,544741,549846,554302,510505,520142,520143,520153,520366,520370,520371,453574,461753,498885,424973'
                ]
            ],
            [
                BankInterface::NAME         =>'Bank UOB Indonesia',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_DB =>'451497',
                    BankBinInterface::BIN_TYPE_CC =>'402695,402736,421445,421920,472629,486463,486607,512765,519311,540579,553388,461981,512620,558323'
                ]
            ],
            [
                BankInterface::NAME         =>'Bank Negara Indonesia (BNI) - American Express',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_CC =>'379991'
                ]
            ],
            [
                BankInterface::NAME         =>'Citibank',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_DB =>'415845,461938,529758,559909',
                    BankBinInterface::BIN_TYPE_CC =>'403731,414009,425864,428107,454178,454179,461778,470544,521551,540184,542177,552042,555018,555163,558720,486637,508102'
                ]
            ],
            [
                BankInterface::NAME         =>'Bank Commonwealth',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_DB =>'523987,513831'
                ]
            ],
            [
                BankInterface::NAME         =>'HSBC',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_CC =>'518323,518494,518535,551543,483576,436317,436318,529442,400934,403409,409675,447211,454493,483575,464993,483574,483577'
                ]
            ],
            [
                BankInterface::NAME         =>'Bank Tabungan Negara (BTN)',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_DB =>'421570,462436,469345,485447'
                ]
            ],
            [
                BankInterface::NAME         =>'Bank Tabungan Pensiunan Nasional',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_DB =>'466160,466101'
                ]
            ],
            [
                BankInterface::NAME         =>'Bank Artha Graha Internasional',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_DB =>'420183',
                    BankBinInterface::BIN_TYPE_CC =>'424103'
                ]
            ],
            [
                BankInterface::NAME         =>'Bank ICBC Indonesia',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_CC =>'436407,436580,436406'
                ]
            ],
            [
                BankInterface::NAME         =>'Bank QNB Indonesia',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_CC =>'408980'
                ]
            ],
            [
                BankInterface::NAME         =>'Bank Sinarmas',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_DB =>'484777,421456',
                    BankBinInterface::BIN_TYPE_CC =>'489370,489372,489373,489374,421469'
                ]
            ],
            [
                BankInterface::NAME         =>'Bank Tamara',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_DB =>'524495,522787',
                    BankBinInterface::BIN_TYPE_CC =>'405515,529737,408978'
                ]
            ],
            [
                BankInterface::NAME         =>'Bank of China',
                BankInterface::BIN_LIST     =>
                [
                    BankBinInterface::BIN_TYPE_DB =>'525841,533659'
                ]
            ]
        ];
        $factory=[];
        $query=[];
        $dataBank = [];
        for($i=0;$i<count($bankData);$i++){
            $dataBank[$i][BankInterface::NAME]      = $bankData[$i][BankInterface::NAME];

            $factory[$i] = $this->bankFactory->create();
            $factory[$i]->addData($dataBank[$i])->save();
            if($factory[$i]->getId()){
                if(isset($bankData[$i][BankInterface::BIN_LIST])){
                    $this->installDataBankBin($factory[$i]->getId(),$bankData[$i][BankInterface::BIN_LIST]);
                }
            }
            
        }
        
    }

    /**
     * Install Bank Bin With Type
     */
    protected function installDataBankBin($bankId,$dataBin){
   
        if(isset($dataBin[BankBinInterface::BIN_TYPE_DB])){ // Debit
            $this->saveBinData($bankId,$dataBin[BankBinInterface::BIN_TYPE_DB],BankBinInterface::BIN_TYPE_DB);
        }

        if(isset($dataBin[BankBinInterface::BIN_TYPE_CC])){ // Credit
            $this->saveBinData($bankId,$dataBin[BankBinInterface::BIN_TYPE_CC],BankBinInterface::BIN_TYPE_CC);
        }
        if(isset($dataBin[BankBinInterface::BIN_TYPE_DBCC])){ // Credit
            $this->saveBinData($bankId,$dataBin[BankBinInterface::BIN_TYPE_DBCC],BankBinInterface::BIN_TYPE_DBCC);
        }
       
    }

    /**
     * Save Parameter Bank Bin 
     */
    protected function saveBinData($bankId,$data,$typeId){
        $param = [];
        $dataList = explode(",",$data);
       
        $i = 0;
        foreach($dataList as $row){
            $param[$i][BankBinInterface::BANK_ID]    =$bankId;
            $param[$i][BankBinInterface::TYPE_ID]    =$typeId;
            $param[$i][BankBinInterface::BIN_CODE]   =preg_replace('/\s+/', '', $row);

            $factory[$i] = $this->bankBinFactory->create();
            $factory[$i]->addData($param[$i])->save();
            $i++;
        }
    }
}
