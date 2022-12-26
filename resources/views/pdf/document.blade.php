<html>
  <head>
    <title>ComPDFKit - Your Invoice</title>
    <meta charset='utf-8'/>
    <meta name="viewport" content="width=device-width,initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
  </head>
  <body style="margin: 0; padding: 0;font-family:Helvetica;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
      <tr>
        <td style="padding: 36px 34px;">
          <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;">
            <tr>
              <td style="padding-bottom: 43px">
                <a target="_blank" href="http://test-pdf-pro.kdan.cn:3026/">
                  <img height="30" src="http://img2.3png.com/c6473288afbfe242dcf021820766db6abf3a.png" alt="invoice-logo">
                </a>
              </td>
            </tr>
            <tr>
              <td style="padding-bottom: 63px">
                <table class="payment-info" cellpadding="0" cellspacing="0" width="600">
                  <tbody>
                    <tr>
                      <td>
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                          <tbody>
                            <tr bgcolor="#F8F8F8">
                              <td style="padding:12px 0 6px 12px;border-radius:4px 0px 0px 0px;">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                  <tbody>
                                    <tr>
                                      <td style="color:#999;font-size:12px;line-height:14px;">Invoice Date</td>
                                    </tr>
                                    <tr>
																			<td height="4"></td>
                                    </tr>
                                    <tr>
                                      <td style="height:18px;color:#333;font-size:14px;line-height:18px;">{{$data['orderdata']['created_at']}}</td>
                                    </tr>
                                  </tbody>
                                </table>
                              </td>
                            </tr>
                            <tr bgcolor="#fff">
															<td height="1"></td>
                            </tr>
                            <tr bgcolor="#F8F8F8">
                              <td style="padding:12px 0 6px 12px;border-radius:4px 0px 0px 0px;">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                  <tbody>
                                    <tr>
                                      <td style="color:#999;font-size:12px;line-height:14px;">Invoice Number</td>
                                    </tr>
                                    <tr><td height="4"></td></tr>
                                    <tr>
                                      <td style="height:18px;color:#333;font-size:14px;line-height:18px;">{{$data['orderdata']['order_no'].time()}}</td>
                                    </tr>
                                  </tbody>
                                </table>
                              </td>
                            </tr>
                            <tr bgcolor="#fff">
															<td height="1"></td>
                            </tr>
                            <tr bgcolor="#F8F8F8">
                              <td style="padding:12px 0 6px 12px;border-radius:4px 0px 0px 0px;">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                  <tbody>
                                    <tr>
                                      <td style="color:#999;font-size:12px;line-height:14px;">Order Number</td>
                                    </tr>
                                    <tr>
																			<td height="4"></td>
                                    </tr>
                                    <tr>
                                      <td style="height:18px;color:#333;font-size:14px;line-height:18px;">{{$data['orderdata']['order_no']}}</td>
                                    </tr>
                                  </tbody>
                                </table>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                      <td width="1" bgcolor="#fff"></td>
                      <td bgcolor="#F8F8F8" style="border-radius:0px 4px 4px 0px;padding-left:12px;" width="50%">
                        <table border="0" cellpadding="0" cellspacing="0">
                          <tbody>
                            <tr><td height="19"></td></tr>
                            <tr>
                              <td style="color:#999;font-size:12px;line-height:14px;">User Emaill</td>
                            </tr>
                            <tr><td height="4"></td></tr>
                            <tr>
                              <?php $youjian=unserialize($data['orderdata']['user_bill']);?>
                              <td style="height:18px;color:#333;font-size:14px;line-height:18px;">{{$youjian['email']}}</td>
                            </tr>
                            <tr><td height="60"></td></tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
            <tr><td style="height: 60px;"></td></tr>
            <tr>
              <td>
                <table cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse; fong-weight: 500; text-align: center;">
                  <tr style="background:rgba(245,245,245,1);font-size: 12px;">
                    <td style="height: 36px;padding-left: 12px;color:#666;text-align: left;">Service</td>
                    <td style="height: 36px;color:#666;">Years</td>
                    <td style="height: 36px;color:#666;">Unit Price</td>
                    <td style="height: 36px;color:#666;">Discount</td>
                    <td style="height: 36px;color:#666;text-align: right;padding-right: 12px;">Amount</td>
                  </tr>
                  <?php $sums=0.00;?>
                    @foreach($data['ordergoodsdata'] as $key => $item)
                  <tr style="height: 85px;font-size:14px;color:#333;">
                    <td style="width:280px;height: 85px;border-bottom: 1px solid #DADADA;">
                      <table>
                        <tr>
                          <td rowspan="2" style="padding-left:12px;"><img src="http://img2.3png.com/c6473288afbfe242dcf021820766db6abf3a.png" alt="mail-logo" style="width: 42px; height: 42px;"></td>
                          <td style="width:280px;font-size:14px;font-weight:bold;text-align: left;line-height:17px;color:#333;">{{$item['goodsname']}}</td>
                        </tr>
                        <tr>
                          <td align="left">
                            <span style="font-size:12px;color:#999;line-height:14px;">
                         One Time Purchase
                            </span>
                          </td>
                        </tr>
                      </table>

                    </td>
                    <td style="font-size:14px;color:#333;border-bottom: 1px solid #DADADA;">{{$item['pay_years']}}</td>
                    <td style="font-family:Helvetica;border-bottom: 1px solid #DADADA;font-size:14px;color:#333;">
                      USD{{$item['price']}}
                    </td>
                    <td style="font-family:Helvetica;border-bottom: 1px solid #DADADA;font-size:14px;color:#333;">
                      USD-0.00
                    </td>
                    <td style="font-family:Helvetica;border-bottom: 1px solid #DADADA;font-size:14px;color:#333;">
                      USD{{$item['price']}}
                    </td>
                    <?php $sums=round($sums+$item['price'],2)?>
                  </tr>
                  @endforeach
                </table>
              </td>
            </tr>

            <tr><td style="height: 30px;"></td></tr>

            <tr style="text-align:right">
              <td style="font-size:18px;color:#333;font-weight:bold;line-height:22px;text-align:right"><span style="color:#666;font-size:14px;">Total&nbsp;&nbsp;&nbsp;&nbsp;</span>{{$sums}}</td>
            </tr>

            <tr><td height="36"></td></tr>

            <tr>
              <td style="text-align:right;color:#999;">This is computer generated invoice no signature required.</td>
            </tr>

            <tr><td style="height: 200px;"></td></tr>

            <tr style="background:rgba(245,245,245,1);">
	            <td>
								<table cellpadding="0" cellspacing="0">
									<tbody>
										<tr>
										  <td style="padding: 20px;padding-bottom:0;">
				                <a target="_blank" href="http://test-pdf-pro.kdan.cn:3026/">
				                  <img height="16" src="http://img2.3png.com/c6473288afbfe242dcf021820766db6abf3a.png" alt="invoice-logo">
				                </a>
				              </td>
										</tr>
										<tr><td height="10"></td></tr>
										<tr>
				              <td style="padding: 20px;padding-top:0;color: #666;">
				                If you have any questions, please contact <a target="_blank" href="mailto:support@compdf.com" style="color: #3285E3;">support@compdf.com</a>.
				              </td>
										</tr>
									</tbody>
								</table>
	            </td>
            </tr>
          </table>
        </td>
      </tr>

    </table>
  </body>
</html>