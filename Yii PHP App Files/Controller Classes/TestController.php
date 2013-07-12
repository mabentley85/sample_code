<?php
/*
 * VERY DUMMY TEST CONTROLLER
 * FOR THE SAKE OF THE EXAMPLE
 * TEST IT AS http : / / <yourapplicationurl> / index.php ? r=test/test
 */
	
class TestController extends Controller
{
     // no layouts here
     public $layout = '';
	 
	 /*
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}
	
	public function accessRules()
	{
		return array(
			array('allow',  // allow Authenticated users to perform 'index' action
				'actions'=>array('xls'),
				'expression'=>'Yii::app()->user->checkAccess("admin")',
			),
			array('allow',  // allow Authenticated users to perform 'index' action
				'actions'=>array('read'),
				'expression'=>'Yii::app()->user->checkAccess("admin")',
			),
			array('allow',  // allow Authenticated users to perform 'index' action
				'actions'=>array('demo'),
				'expression'=>'Yii::app()->user->checkAccess("admin")',
			),
			array('allow',  // allow Authenticated users to perform 'index' action
				'actions'=>array('readXls'),
				'expression'=>'Yii::app()->user->checkAccess("admin")',
			),
			array('allow',  // allow Authenticated users to perform 'index' action
				'actions'=>array('dbSave'),
				'expression'=>'Yii::app()->user->checkAccess("admin")',
			),
			array('allow',  // allow Authenticated users to perform 'index' action
				'actions'=>array('readAll'),
				'expression'=>'Yii::app()->user->checkAccess("admin")',
			),
			array('allow',  // allow Authenticated users to perform 'index' action
				'actions'=>array('csv'),
				'expression'=>'Yii::app()->user->checkAccess("admin")',
			),
			array('allow',  // allow Authenticated users to perform 'index' action
				'actions'=>array('simpleSheet'),
				'expression'=>'Yii::app()->user->checkAccess("admin")',
			),
			array('allow',  // allow Authenticated users to perform 'index' action
				'actions'=>array('upload'),
				'expression'=>'Yii::app()->user->checkAccess("admin")',
			),
			array('allow',  // allow Authenticated users to perform 'index' action
				'actions'=>array('uploadXls'),
				'expression'=>'Yii::app()->user->checkAccess("admin")',
			),
		);
	}
	 
	 
	 public function actionXls()
	 	{
	 			
	 		error_reporting(E_ALL);
			date_default_timezone_set('Europe/London');
			
			// get a reference to the path of PHPExcel classes
			$phpExcelPath = Yii::getPathOfAlias('ext');
			//print $phpExcelPath . '/PHPExcel.php<br />';
			
			// Turn off our amazing library autoload
			spl_autoload_unregister(array('YiiBase','autoload'));
			
			/** PHPExcel */
			require_once $phpExcelPath . '/PHPExcel.php';
			
			/*
			 * making use of our reference, include the main class
			 * when we do this, phpExcel has its own autoload registration
			 * procedure (PHPExcel_Autoloader::Register();)
			 * include($phpExcelPath . DIRECTORY_SEPARATOR . 'PHPExcel.php');
			 */
			
			// Create new PHPExcel object
			$objPHPExcel = new PHPExcel();
			
			// Set properties
			$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");
							 
			// Add some data
			$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue('A1', 'Hello')
							->setCellValue('B2', 'world!')
							->setCellValue('C1', 'Hello')
							->setCellValue('D2', 'world!');
			
			// Miscellaneous glyphs, UTF-8
			$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue('A4', 'Miscellaneous glyphs')
							->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');
							
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('Simple');
			
			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);
			
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="01simple.xls"');
			header('Cache-Control: max-age=0');
			
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save($phpExcelPath);
			print "Done.";
			
			/*
			 * Once we have finished using the library, give back the
			 * power to Yii...
			 */
			spl_autoload_register(array('YiiBase','autoload'));
			
	}

	public function actionRead()
		{
			error_reporting(E_ALL);
			date_default_timezone_set('Europe/London');
			
			// get a reference to the path of PHPExcel classes
			$phpExcelPath = Yii::getPathOfAlias('ext');
			
			// Turn off our amazing library autoload
			spl_autoload_unregister(array('YiiBase','autoload'));
			
			/** PHPExcel_IOFactory */
			require_once  $phpExcelPath . '/PHPExcel/IOFactory.php';
			
			if (!file_exists("05featuredemo.xlsx"))
				{
					exit("Please run 05featuredemo.php first.<br />");
				}
			
			echo date('H:i:s') . " Load from Excel2007 file<br />";
			$objPHPExcel = PHPExcel_IOFactory::load("05featuredemo.xlsx");
			
			$objWorksheet = $objPHPExcel->getActiveSheet();
			echo '<table border="1" >';
			foreach ($objWorksheet->getRowIterator() as $row)
				{
					echo '<tr>' . "\n";
					$cellIterator = $row->getCellIterator();
					$cellIterator->setIterateOnlyExistingCells(false); 
					/*
					 * This loops all cells, even if it is not set.
					 * By default, only cells that are set will be
					 * iterated.
					 */
					
					foreach ($cellIterator as $cell)
						{
							echo '<td>' . $cell->getValue() . '</td>' . "\n";
						}
					
					echo '</tr>' . "\n";
				}
			
			echo '</table>' . "\n";
						
			/*
			echo date('H:i:s') . " Write to Excel2007 format<br />";
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
			
			// Echo memory peak usage
			echo date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB\r<br />";
			
			// Echo done
			echo date('H:i:s') . " Done writing files.\r<br />";
			*/
			/*
			 * Once we have finished using the library, give back the
			 * power to Yii...
			 */
			spl_autoload_register(array('YiiBase','autoload'));			
		}

	public function actionReadXls()
		{
			error_reporting(E_ALL);
			date_default_timezone_set('Europe/London');
			
			// get a reference to the path of PHPExcel classes
			$phpExcelPath = Yii::getPathOfAlias('ext');
			
			// Turn off our amazing library autoload
			spl_autoload_unregister(array('YiiBase','autoload'));
			
			/** PHPExcel_IOFactory */
			require_once  $phpExcelPath . '/PHPExcel/IOFactory.php';
			
			if (!file_exists("simple.xls"))
				{
					exit("Please run 05featuredemo.php first.<br />");
				}
			
			$objPHPExcel = PHPExcel_IOFactory::load("simple.xls");
			echo date('H:i:s') . " Load from Excel5 file<br />";
			
			/*
			echo date('H:i:s') . " Write to Excel2007 format<br />";
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
			
			// Echo memory peak usage
			echo date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB\r<br />";
			
			// Echo done
			echo date('H:i:s') . " Done writing files.\r<br />";
			*/
			
			/*
			 * Once we have finished using the library, give back the
			 * power to Yii...
			 */
			spl_autoload_register(array('YiiBase','autoload'));			
		}

	public function actionDemo()
		{
			/** Error reporting */
			error_reporting(E_ALL);
			date_default_timezone_set('Europe/London');
			
			// get a reference to the path of PHPExcel classes
			$phpExcelPath = Yii::getPathOfAlias('ext');
			
			// Turn off our amazing library autoload
			spl_autoload_unregister(array('YiiBase','autoload'));
			
			// Creating the xls
						
			/** PHPExcel */
			require_once $phpExcelPath .  '/PHPExcel.php';
			
			// Create new PHPExcel object
			echo date('H:i:s') . " Create new PHPExcel object<br />";
			$objPHPExcel = new PHPExcel();
			
			// Set properties
			echo date('H:i:s') . " Set properties<br />";
			$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");
							 
			// Create a first sheet, representing sales data
			echo date('H:i:s') . " Add some data<br />";
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Invoice');
			$objPHPExcel->getActiveSheet()->setCellValue('D1', PHPExcel_Shared_Date::PHPToExcel( gmmktime(0,0,0,date('m'),date('d'),date('Y')) ));
			$objPHPExcel->getActiveSheet()->getStyle('D1')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
			$objPHPExcel->getActiveSheet()->setCellValue('E1', '#12566');
			
			$objPHPExcel->getActiveSheet()->setCellValue('A3', 'Product Id');
			$objPHPExcel->getActiveSheet()->setCellValue('B3', 'Description');
			$objPHPExcel->getActiveSheet()->setCellValue('C3', 'Price');
			$objPHPExcel->getActiveSheet()->setCellValue('D3', 'Amount');
			$objPHPExcel->getActiveSheet()->setCellValue('E3', 'Total');
			
			$objPHPExcel->getActiveSheet()->setCellValue('A4', '1001');
			$objPHPExcel->getActiveSheet()->setCellValue('B4', 'PHP for dummies');
			$objPHPExcel->getActiveSheet()->setCellValue('C4', '20');
			$objPHPExcel->getActiveSheet()->setCellValue('D4', '1');
			$objPHPExcel->getActiveSheet()->setCellValue('E4', '=C4*D4');
			
			$objPHPExcel->getActiveSheet()->setCellValue('A5', '1012');
			$objPHPExcel->getActiveSheet()->setCellValue('B5', 'OpenXML for dummies');
			$objPHPExcel->getActiveSheet()->setCellValue('C5', '22');
			$objPHPExcel->getActiveSheet()->setCellValue('D5', '2');
			$objPHPExcel->getActiveSheet()->setCellValue('E5', '=C5*D5');
			
			$objPHPExcel->getActiveSheet()->setCellValue('E6', '=C6*D6');
			$objPHPExcel->getActiveSheet()->setCellValue('E7', '=C7*D7');
			$objPHPExcel->getActiveSheet()->setCellValue('E8', '=C8*D8');
			$objPHPExcel->getActiveSheet()->setCellValue('E9', '=C9*D9');
			
			$objPHPExcel->getActiveSheet()->setCellValue('D11', 'Total excl.:');
			$objPHPExcel->getActiveSheet()->setCellValue('E11', '=SUM(E4:E9)');
			
			$objPHPExcel->getActiveSheet()->setCellValue('D12', 'VAT:');
			$objPHPExcel->getActiveSheet()->setCellValue('E12', '=E11*0.21');
			
			$objPHPExcel->getActiveSheet()->setCellValue('D13', 'Total incl.:');
			$objPHPExcel->getActiveSheet()->setCellValue('E13', '=E11+E12');
			
			// Add comment
			echo date('H:i:s') . " Add comments<br />";
			
			$objPHPExcel->getActiveSheet()->getComment('E11')->setAuthor('PHPExcel');
			$objCommentRichText = $objPHPExcel->getActiveSheet()->getComment('E11')->getText()->createTextRun('PHPExcel:');
			$objCommentRichText->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getComment('E11')->getText()->createTextRun("\r<br />");
			$objPHPExcel->getActiveSheet()->getComment('E11')->getText()->createTextRun('Total amount on the current invoice, excluding VAT.');
			
			$objPHPExcel->getActiveSheet()->getComment('E12')->setAuthor('PHPExcel');
			$objCommentRichText = $objPHPExcel->getActiveSheet()->getComment('E12')->getText()->createTextRun('PHPExcel:');
			$objCommentRichText->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getComment('E12')->getText()->createTextRun("\r<br />");
			$objPHPExcel->getActiveSheet()->getComment('E12')->getText()->createTextRun('Total amount of VAT on the current invoice.');
			
			$objPHPExcel->getActiveSheet()->getComment('E13')->setAuthor('PHPExcel');
			$objCommentRichText = $objPHPExcel->getActiveSheet()->getComment('E13')->getText()->createTextRun('PHPExcel:');
			$objCommentRichText->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getComment('E13')->getText()->createTextRun("\r<br />");
			$objPHPExcel->getActiveSheet()->getComment('E13')->getText()->createTextRun('Total amount on the current invoice, including VAT.');
			$objPHPExcel->getActiveSheet()->getComment('E13')->setWidth('100pt');
			$objPHPExcel->getActiveSheet()->getComment('E13')->setHeight('100pt');
			$objPHPExcel->getActiveSheet()->getComment('E13')->setMarginLeft('150pt');
			$objPHPExcel->getActiveSheet()->getComment('E13')->getFillColor()->setRGB('EEEEEE');
			
			
			// Add rich-text string
			echo date('H:i:s') . " Add rich-text string<br />";
			$objRichText = new PHPExcel_RichText();
			$objRichText->createText('This invoice is ');
			
			$objPayable = $objRichText->createTextRun('payable within thirty days after the end of the month');
			$objPayable->getFont()->setBold(true);
			$objPayable->getFont()->setItalic(true);
			$objPayable->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN ) );
			
			$objRichText->createText(', unless specified otherwise on the invoice.');
			
			$objPHPExcel->getActiveSheet()->getCell('A18')->setValue($objRichText);
			
			// Merge cells
			echo date('H:i:s') . " Merge cells<br />";
			$objPHPExcel->getActiveSheet()->mergeCells('A18:E22');
			$objPHPExcel->getActiveSheet()->mergeCells('A28:B28');		// Just to test...
			$objPHPExcel->getActiveSheet()->unmergeCells('A28:B28');	// Just to test...
			
			// Protect cells
			echo date('H:i:s') . " Protect cells<br />";
			$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);	// Needs to be set to true in order to enable any worksheet protection!
			$objPHPExcel->getActiveSheet()->protectCells('A3:E13', 'PHPExcel');
			
			// Set cell number formats
			echo date('H:i:s') . " Set cell number formats<br />";
			$objPHPExcel->getActiveSheet()->getStyle('E4:E13')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);
			
			// Set column widths
			echo date('H:i:s') . " Set column widths<br />";
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
			
			// Set fonts
			echo date('H:i:s') . " Set fonts<br />";
			$objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setName('Candara');
			$objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setSize(20);
			$objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
			
			$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
			$objPHPExcel->getActiveSheet()->getStyle('E1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
			
			$objPHPExcel->getActiveSheet()->getStyle('D13')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('E13')->getFont()->setBold(true);
			
			// Set alignments
			echo date('H:i:s') . " Set alignments<br />";
			$objPHPExcel->getActiveSheet()->getStyle('D11')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('D12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('D13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			$objPHPExcel->getActiveSheet()->getStyle('A18')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
			$objPHPExcel->getActiveSheet()->getStyle('A18')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			
			$objPHPExcel->getActiveSheet()->getStyle('B5')->getAlignment()->setShrinkToFit(true);
			
			// Set thin black border outline around column
			echo date('H:i:s') . " Set thin black border outline around column<br />";
			$styleThinBlackBorderOutline = array(
				'borders' => array(
					'outline' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('argb' => 'FF000000'),
					),
				),
			);
			$objPHPExcel->getActiveSheet()->getStyle('A4:E10')->applyFromArray($styleThinBlackBorderOutline);
			
			
			// Set thick brown border outline around "Total"
			echo date('H:i:s') . " Set thick brown border outline around Total<br />";
			$styleThickBrownBorderOutline = array(
				'borders' => array(
					'outline' => array(
						'style' => PHPExcel_Style_Border::BORDER_THICK,
						'color' => array('argb' => 'FF993300'),
					),
				),
			);
			$objPHPExcel->getActiveSheet()->getStyle('D13:E13')->applyFromArray($styleThickBrownBorderOutline);
			
			// Set fills
			echo date('H:i:s') . " Set fills<br />";
			$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFill()->getStartColor()->setARGB('FF808080');
			
			// Set style for header row using alternative method
			echo date('H:i:s') . " Set style for header row using alternative method<br />";
			$objPHPExcel->getActiveSheet()->getStyle('A3:E3')->applyFromArray(
					array(
						'font'    => array(
							'bold'      => true
						),
						'alignment' => array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
						),
						'borders' => array(
							'top'     => array(
			 					'style' => PHPExcel_Style_Border::BORDER_THIN
			 				)
						),
						'fill' => array(
				 			'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
				  			'rotation'   => 90,
				 			'startcolor' => array(
				 				'argb' => 'FFA0A0A0'
				 			),
				 			'endcolor'   => array(
				 				'argb' => 'FFFFFFFF'
				 			)
				 		)
					)
			);
			
			$objPHPExcel->getActiveSheet()->getStyle('A3')->applyFromArray(
					array(
						'alignment' => array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
						),
						'borders' => array(
							'left'     => array(
			 					'style' => PHPExcel_Style_Border::BORDER_THIN
			 				)
						)
					)
			);
			
			$objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray(
					array(
						'alignment' => array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
						)
					)
			);
			
			$objPHPExcel->getActiveSheet()->getStyle('E3')->applyFromArray(
					array(
						'borders' => array(
							'right'     => array(
			 					'style' => PHPExcel_Style_Border::BORDER_THIN
			 				)
						)
					)
			);
			
			// Unprotect a cell
			echo date('H:i:s') . " Unprotect a cell<br />";
			$objPHPExcel->getActiveSheet()->getStyle('B1')->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
			
			// Add a hyperlink to the sheet
			echo date('H:i:s') . " Add a hyperlink to the sheet<br />";
			$objPHPExcel->getActiveSheet()->setCellValue('E26', 'www.phpexcel.net');
			$objPHPExcel->getActiveSheet()->getCell('E26')->getHyperlink()->setUrl('http://www.phpexcel.net');
			$objPHPExcel->getActiveSheet()->getCell('E26')->getHyperlink()->setTooltip('Navigate to website');
			$objPHPExcel->getActiveSheet()->getStyle('E26')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			$objPHPExcel->getActiveSheet()->setCellValue('E27', 'Terms and conditions');
			$objPHPExcel->getActiveSheet()->getCell('E27')->getHyperlink()->setUrl("sheet://'Terms and conditions'!A1");
			$objPHPExcel->getActiveSheet()->getCell('E27')->getHyperlink()->setTooltip('Review terms and conditions');
			$objPHPExcel->getActiveSheet()->getStyle('E27')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			// Add a drawing to the worksheet
			/*
			echo date('H:i:s') . " Add a drawing to the worksheet<br />";
					$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Logo');
			$objDrawing->setDescription('Logo');
			$objDrawing->setPath('./images/officelogo.jpg');
			$objDrawing->setHeight(36);
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
			
			// Add a drawing to the worksheet
			echo date('H:i:s') . " Add a drawing to the worksheet<br />";
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Paid');
			$objDrawing->setDescription('Paid');
			$objDrawing->setPath('./images/paid.png');
			$objDrawing->setCoordinates('B15');
			$objDrawing->setOffsetX(110);
			$objDrawing->setRotation(25);
			$objDrawing->getShadow()->setVisible(true);
			$objDrawing->getShadow()->setDirection(45);
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
			
			// Add a drawing to the worksheet
			echo date('H:i:s') . " Add a drawing to the worksheet<br />";
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('PHPExcel logo');
			$objDrawing->setDescription('PHPExcel logo');
			$objDrawing->setPath('./images/phpexcel_logo.gif');
			$objDrawing->setHeight(36);
			$objDrawing->setCoordinates('D24');
			$objDrawing->setOffsetX(10);
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
			*/
			
			// Play around with inserting and removing rows and columns
			echo date('H:i:s') . " Play around with inserting and removing rows and columns<br />";
			$objPHPExcel->getActiveSheet()->insertNewRowBefore(6, 10);
			$objPHPExcel->getActiveSheet()->removeRow(6, 10);
			$objPHPExcel->getActiveSheet()->insertNewColumnBefore('E', 5);
			$objPHPExcel->getActiveSheet()->removeColumn('E', 5);
			
			// Set header and footer. When no different headers for odd/even are used, odd header is assumed.
			echo date('H:i:s') . " Set header/footer<br />";
			$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BInvoice&RPrinted on &D');
			$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $objPHPExcel->getProperties()->getTitle() . '&RPage &P of &N');
			
			// Set page orientation and size
			echo date('H:i:s') . " Set page orientation and size<br />";
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			
			// Rename sheet
			echo date('H:i:s') . " Rename sheet<br />";
			$objPHPExcel->getActiveSheet()->setTitle('Invoice');
			
			
			// Create a new worksheet, after the default sheet
			echo date('H:i:s') . " Create new Worksheet object<br />";
			$objPHPExcel->createSheet();
			
			// Llorem ipsum...
			$sLloremIpsum = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Vivamus eget ante. Sed cursus nunc semper tortor. Aliquam luctus purus non elit. Fusce vel elit commodo sapien dignissim dignissim. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Curabitur accumsan magna sed massa. Nullam bibendum quam ac ipsum. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Proin augue. Praesent malesuada justo sed orci. Pellentesque lacus ligula, sodales quis, ultricies a, ultricies vitae, elit. Sed luctus consectetuer dolor. Vivamus vel sem ut nisi sodales accumsan. Nunc et felis. Suspendisse semper viverra odio. Morbi at odio. Integer a orci a purus venenatis molestie. Nam mattis. Praesent rhoncus, nisi vel mattis auctor, neque nisi faucibus sem, non dapibus elit pede ac nisl. Cras turpis.';
			
			// Add some data to the second sheet, resembling some different data types
			echo date('H:i:s') . " Add some data<br />";
			$objPHPExcel->setActiveSheetIndex(1);
			$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Terms and conditions');
			$objPHPExcel->getActiveSheet()->setCellValue('A3', $sLloremIpsum);
			$objPHPExcel->getActiveSheet()->setCellValue('A4', $sLloremIpsum);
			$objPHPExcel->getActiveSheet()->setCellValue('A5', $sLloremIpsum);
			$objPHPExcel->getActiveSheet()->setCellValue('A6', $sLloremIpsum);
			
			// Set the worksheet tab color
			echo date('H:i:s') . " Set the worksheet tab color<br />";
			$objPHPExcel->getActiveSheet()->getTabColor()->setARGB('FF0094FF');;
			
			// Set alignments
			echo date('H:i:s') . " Set alignments<br />";
			$objPHPExcel->getActiveSheet()->getStyle('A3:A6')->getAlignment()->setWrapText(true);
			
			// Set column widths
			echo date('H:i:s') . " Set column widths<br />";
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(80);
			
			// Set fonts
			echo date('H:i:s') . " Set fonts<br />";
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setName('Candara');
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			
			$objPHPExcel->getActiveSheet()->getStyle('A3:A6')->getFont()->setSize(8);
			
			// Add a drawing to the worksheet
			/*
			echo date('H:i:s') . " Add a drawing to the worksheet<br />";
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Terms and conditions');
			$objDrawing->setDescription('Terms and conditions');
			$objDrawing->setPath('./images/termsconditions.jpg');
			$objDrawing->setCoordinates('B14');
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
			*/
			
			// Set page orientation and size
			echo date('H:i:s') . " Set page orientation and size<br />";
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			
			// Rename sheet
			echo date('H:i:s') . " Rename sheet<br />";
			$objPHPExcel->getActiveSheet()->setTitle('Terms and conditions');
			
			
			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);
			///////
			
			/** PHPExcel_IOFactory */
			require_once $phpExcelPath . '/PHPExcel/IOFactory.php';
			
			// Save Excel 2007 file
			echo date('H:i:s') . " Write to Excel2007 format<br />";
			/* Write to Excel2007
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save(str_replace('.php', '.xlsx', '05featuredemo.xlsx'));
			*/
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save(str_replace('.php', '.xls', '05featuredemo.xls'));
			
			// Echo memory peak usage
			echo date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB\r<br />";
			
			// Echo done
			echo date('H:i:s') . " Done writing file.\r<br />";
			
			/*
			 * Once we have finished using the library, give back the
			 * power to Yii...
			 */
			spl_autoload_register(array('YiiBase','autoload'));
			
		}

		public function actionIndex()
			{
				// Renders in the index view
				$this->render('index');
			
			}
		
		public function actionUpload()
			{
				$file = CUploadedFile::getInstanceByName('Application[upload]');
				$type = $file->getType();
				$size = $file->getSize();
				$name = $file->getName();
				$temp = $file->getTempName();
				echo "File Name: " . $name . "<br />";
				echo "Temp Name: " . $temp . "<br />";
				echo "File Type: " . $type . "<br />";
				echo "File Size: " . $size . " Bytes<br />";
				$savePath = 'temp/' . $file->name;
				echo "Save Path: " . $savePath . "<br />";
				$file->saveAs($savePath, TRUE); // Starts at Page base directory
				echo "File Saved.<br />";
				echo CHtml::image('../../' . $savePath); // Relative to the current controller file (why I added "../../")
				//echo '<img src="../../images/tt_logo.png" />
			}
		
		public function actionUploadXls()
		{
			$file = CUploadedFile::getInstancesByName('Application[upload]');
			
		}
		
		public function actionSimpleSheet()
			{
				error_reporting(E_ALL);
				date_default_timezone_set('Europe/London');
				
				// get a reference to the path of PHPExcel classes
				$phpExcelPath = Yii::getPathOfAlias('ext');
				print $phpExcelPath . '/PHPExcel.php<br />';
				
				// Turn off our amazing library autoload
				spl_autoload_unregister(array('YiiBase','autoload'));
				print "Auto-Load Un-did.<br />";
								/** PHPExcel */
				require_once $phpExcelPath . '/PHPExcel.php';
				print "PHPExcel Loaded.<br />";
				
				/** PHPExcel_IOFactory */
				require_once $phpExcelPath . '/PHPExcel/IOFactory.php';
				print "IO Factory Loaded.<br />";
				
				// Load a File
				$inputFileName = './simple.xls';
				if(file_exists($inputFileName))
					{
						print "File Name Set and Path Set: " . $inputFileName . ".<br />";
					}
				
				/**  Identify the type of $inputFileName  **/
				$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
				print "File Type Identified as: " . $inputFileType . "<br />";
				
				
				/**  Create a new Reader of the type that has been identified  **/
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);
				print "New PHPExcel Reader Created for " . $inputFileType . ".<br />";
				
				/**  Load $inputFileName to a PHPExcel Object  **/
				$objPHPExcel = $objReader->load("./simple.xls");
				print "File Loaded!!!<br />";
								
			
				/*
				 * Once we have finished using the library, give back the
				 * power to Yii...
				 */
				spl_autoload_register(array('YiiBase','autoload'));
				
				// All Done
				echo "Done.<br />";
				
			}

	public function actionCsv()
	{
		error_reporting(E_ALL);
		date_default_timezone_set('Europe/London');
		
		// get a reference to the path of PHPExcel classes
		$phpExcelPath = Yii::getPathOfAlias('ext');
		print $phpExcelPath . '/PHPExcel.php<br />';
		
		// Turn off our amazing library autoload
		spl_autoload_unregister(array('YiiBase','autoload'));
		print "Auto-Load Un-did.<br />";
		
		/** PHPExcel */
		require_once $phpExcelPath . '/PHPExcel.php';
		print "PHPExcel Loaded.<br />";
		
		/** PHPExcel_IOFactory */
		require_once $phpExcelPath . '/PHPExcel/IOFactory.php';
		print "IO Factory Loaded.<br />";
		
		$inputFileType = 'CSV';
		$inputFileName = 'simple.csv';
		
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		print "Reader Created.<br />";
		
		$objPHPExcel = $objReader->load($inputFileName);
		print "CSV File Loaded!!!<br />";
		
		$worksheet = $objPHPExcel->getActiveSheet();
		
		foreach ($worksheet->getRowIterator() as $row)
			{
				//echo 'Row number: ' . $row->getRowIndex();
				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
				
				foreach ($cellIterator as $cell)
					{
						if (!is_null($cell))
							{
								echo 'Cell: ' . $cell->getCoordinate() . ' - ' . $cell->getValue() . "\r\n";
							}
					}
				echo "<br />";
			}
		}
	
	public function actionReadAll()
		{
			error_reporting(E_ALL);
			
			// get a reference to the path of PHPExcel classes
			$phpExcelPath = Yii::getPathOfAlias('ext');
			print $phpExcelPath . '/PHPExcel.php<br />';
			
			// Turn off our amazing library autoload
			spl_autoload_unregister(array('YiiBase','autoload'));
			print "Auto-Load Un-did.<br />";
			
			/** PHPExcel */
			require_once $phpExcelPath . '/PHPExcel.php';
			print "PHPExcel Loaded.<br />";
			
			/** PHPExcel_IOFactory */
			require_once $phpExcelPath . '/PHPExcel/IOFactory.php';
			print "IO Factory Loaded.<br />";
			
			/** File to be imported **/
			$fileName = "test.csv";
			if(file_exists($fileName))
				{
					print "File to be Loaded: " . $fileName . "<br />";
				}
			
			/**  Identify the type of $inputFileName  **/
			$inputFileType = PHPExcel_IOFactory::identify($fileName);
			print "File Type Identified as: " . $inputFileType . "<br />";
			
			/**  Create a new Reader of the type that has been identified  **/
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			print "New PHPExcel Reader Created for " . $inputFileType . ".<br />";
			
			if($inputFileType != "CSV")
				{
					$objReader->setReadDataOnly(TRUE);
					print "Set to Read Data Only<br />";
				}
						
			$objPHPExcel = $objReader->load($fileName);
			print "File Loaded!!!<br />";
			
			/** Begin Iterating through spreadsheet **/
			$worksheet = $objPHPExcel->getActiveSheet();
			echo "<table border='1'>";
			foreach ($worksheet->getRowIterator() as $row)
				{
					//echo 'Row number: ' . $row->getRowIndex();
					$cellIterator = $row->getCellIterator();
					$cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
					// Create New DB Record
					spl_autoload_register(array('YiiBase','autoload'));
					$model = new Student;
					
					echo "<tr>";
					$i = 0;
					foreach ($cellIterator as $cell)
						{
							if(!is_null($cell))
								{
									echo '<td>' . $cell->getValue() . '</td>';
									$value[$i] = $cell->getValue();
									$i++; 
								}
						}
					echo "</tr>";
					
					// Save DB Record
					
					$model->school_id = $value[0];
					$model->school_issued_id = $value[1];
					$model->student_first_name = $value[2];
					$model->student_last_name = $value[3];
					$model->create_time = time();
					$model->update_user = '2';
					$model->isDeleted = '0';
					$model->save();
					
				}
			echo "</table>";
			print "End of SpreadSheet.<br />";
			
			/*
			 * Once we have finished using the library, give back the
			 * power to Yii...
			 */
						
			print "New DB Record Saved!!<br />";
			
			/** Prepare to save as another format **/
			/*
			spl_autoload_unregister(array('YiiBase','autoload'));
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV'); // CSV can be replaced with Excel5, Excel2007 or others
			print "Excel5 Writer Created<br />"; 
			
			$objWriter->save(str_replace('.php', '.csv', 'test.csv'));  // Remember to change file extensions when converting
			print "File saved as CSV Format .csv<br />";
			
			spl_autoload_register(array('YiiBase','autoload'));
			*/
			
		}

	public function actionDbSave()
		{
			$model = new Student;
			$model->school_id = '1';
			$model->school_issued_id = "1234";
			$model->student_first_name = 'New';
			$model->student_last_name = 'Kid';
			$model->create_time = time();
			$model->update_user = '2';
			$model->isDeleted = '0';
			$model->save();
			
			print "New DB Record Saved!!<br />";			
			
		}
		
	public function actionTime()
		{
			//print "GMT: " . gmdate('H:i:s') . "<br />";
			//print "Date: " . date('H:i:s') . "<br />";
			//print gmdate("h", mktime(-3)) . "<br />";
			date_default_timezone_set('America/Denver');
			print date('l jS \of F Y h:i:s A') . "<br />";
		}
			
}