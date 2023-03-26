              <!-- begin:: Content -->
              <div class="kt-section">
              <?php if($this->session->flashdata('emailError')) {?>
                  <div class="alert alert-info fade show" role="alert">
                    <div class="alert-icon"><i class="flaticon-questions-circular-button"></i></div>
                    <div class="alert-text"><?php echo $this->session->flashdata('emailError');?></div>
                    <div class="alert-close">
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true"><i class="la la-close"></i></span>
                      </button>
                    </div>
                  </div>
                  <?php } ?>
                <div class = "kt-container kt-grid__item mt-5">
                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        How to create a free account?
                        </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      It is very easy to create a free account on our site. To start, click on the "Sign Up" button on the right of the navigation bar, then simply enter your username (preferably an easy name to remember) as well as a secure password that you will need to save locally and never give to anyone. Before validating your account you must read and accept the general conditions of the site then check the confirmation box and validate the registration form by clicking on "Sign Up". And There you go ! you are ready to start your journey with us !
                      </div>
                    </div>
                  </div>
                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        Does this site offer subscriptions?
                        </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      No, this site does not offer any subscriptions to its users at this time.                      </div>
                    </div>
                  </div>
                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        Does a file contain an entire season ?                        </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      Each file is intended to contain an entire season, regardless of the sport. The files containing the current seasons are updated gradually after each match day, from the start to the end of the season. The number of games played is displayed under the "GAME PLAYED" column of the search results table. One match played corresponds to one row of the product file.                      </div>
                    </div>
                  </div>
                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        What data is contained in each file ?                        </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      Each file is divided the same, regardless of the sport. The Excel table is read from left to right, each row corresponds to a match, each column corresponds to match summary information or detailed statistic.
                      <br><br>From left to right you will find in each file:
                      <br><br>Match summary and results + Detailed statistics by playing time
                      <br><br>Example for football: Match summary with results -> Home team 1st half time statistics -> Away team 1st half time statistics -> Home team 2nd half time statistics -> Away team 2nd half time statistics -> Home team extra time statistics -> Away team extra time statistics.
                      <br><br>Each game time has several statistics, we have retained certain standard criteria so as not to make our files difficult to read for users. The criteria retained by excelsportdata for each playing time in football, for example, are as follows:
                      <br><br>Ball possession – Goal attempts – Shots on goal	 - Shots off goal - Blocked shot - Free kicks – Corner kicks – Offsides – Throw in – Goalkeeper saves – Fouls – Red cards – Yellow cards – Total passes – Completed passes – Tackles – Attacks – Dangerous attacks

                      <br><br>It is possible that some seasons or matches of the same season have less data, if your Excel table shows an entire column filled with 0, it means that we were not able to obtain more detailed information for the season in question.
                      <br><br>Generally the seasons from the year 2018 have the maximum amount of data available.
                      <br><br>You will find details of the data available for each data file regardless of the sport. You can download a free sample of it by clicking on "download sample" after registering for free or logging into your user account. The details of each file appear in the commentary part of the sample.
                      </div>
                    </div>
                  </div>
                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        Can I see a sample of the file I want to purchase ?                        </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      Yes, after creating your free user account, you can download a sample of each file offered on our site by clicking on the “download sample” link of the product file you wish to purchase.                      </div>
                    </div>
                  </div>

                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        What do the data samples contain ?                        </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                        For each product offered on our site, you have the possibility, by creating a free account, to download a free sample of data in order to know more precisely the data available within the paid file. A sample file contains the same data (columns) as the paid file but is limited to 10 rows (10 matches) and a detailed comment is available inside each data file.                      
                      </div>
                    </div>
                  </div>

                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        How many seasons does a file contain ?                      
                      </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      Each file contains an entire season, which represents several hundred matches varying by sport.
To know exactly the number of matches available in a file, please consult the "GAME PLAYED" column of the results table of your search, on the home page of the site.
                      </div>
                    </div>
                  </div>

                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        Are the seasons updated?   
                      </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      Absolutely, every sport is updated after every match day.
                      </div>
                    </div>
                  </div>

                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        How long do I have access to my purchased files ?
                      </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      The files you have purchased are available without time limit in your user account under "My downloads".
                      </div>
                    </div>
                  </div>


                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        How can i download my data file?
                      </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      After finalizing and paying for your order, you can find your purchased files in your customer account under  "My downloads".
                      </div>
                    </div>
                  </div>

                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        Can i sell the files purchased on this site?
                      </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      It is strictly forbidden to sell the files purchased on our site, the use of our data is reserved exclusively for personal use, users engaging in such acts will be permanently denied access to the site and expose themselves to legal proceedings.
Your files are personal and should remain so.

                      </div>
                    </div>
                  </div>


                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        Can I distribute my purchased files for free?
                      </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      It is strictly forbidden to distribute the files purchased on our site, the use of our data is reserved exclusively for private use, users engaging in such acts will be permanently denied access to the site and expose to legal proceedings.
Your files are personal and should remain so.

                      </div>
                    </div>
                  </div>

                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        Who are our files intended for ?
                      </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      Excelsportdata targets all Internet users wishing to broaden their fields of analysis of sports events.
                      </div>
                    </div>
                  </div>

                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        What is the format of our data files ?
                      </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      All product files downloaded from our site are in Excel format (.xslx)
                      </div>
                    </div>
                  </div>


                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        Are they compatible with my Excel version ?
                      </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      Our files are compatible with all versions of Excel, however there may be some exceptions. We advise you before your first purchase from us to check the compatibility of our files with your version of the Excel software by downloading a free sample data. If the sample opens normally then your version is compatible with our products.
If this is not the case, we invite you to update your version of the Excel software and try again.

                      </div>
                    </div>
                  </div>

                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        Is it important to fully read the general conditions of sale before using this site ?
                      </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      It is essential to be aware of the conditions of sale of our site in order to understand how it works, the rules for using our systems and to avoid any unnecessary disputes. It will take you 15 minutes.
                      </div>
                    </div>
                  </div>


                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        Can files contain errors?                      </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      We work tirelessly to provide you with the most complete and reliable file possible, despite our best efforts, in rare cases and for certain data only, the data may be incomplete or incorrect.
<br>If you encounter an error you should contact us as soon as possible at: help (at) excelsportdata.com so that we can verify and correct the error. A corrected file will be sent to you immediately after our verification.
                      </div>
                    </div>
                  </div>


                  
                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        Do all seasons contain statistics?           </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      Yes, all seasons contain match statistics, the number of these statistics may vary from the oldest to the most recent seasons. Usually the seasons after 2017 contain the maximum amount of data available. But you must verify on the sample file that the data you are looking for is available
                      </div>
                    </div>
                  </div>


                   
                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        Do I have to create a free account before purchasing a product ?     </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      Yes, you must first create a free account before you can purchase any product or download a free sample from our site.
                      </div>
                    </div>
                  </div>


                   
                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        What are the payment methods available ?    </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      You can pay for your purchases by credit card or via Paypal, other payment methods will be added to our system soon.
                      </div>
                    </div>
                  </div>

                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        Is the payment secured ?   </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      All payments made on our site are processed by our respective partners, Stripe and Paypal. We have selected them for their very high level of security, moreover, Excelsportdata does not store the banking data of its users.
                      </div>
                    </div>
                  </div>

                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        How do I download my data files after payment ?  </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      To download your files, nothing could be simpler: Go to your customer area after your payment by clicking on your username in the navigation bar at the top of the site, then click on "My Download" and find all the files you purchased. You just have to choose the file you want to download and click on "Download" to the right of each product.                      </div>
                    </div>
                  </div>

                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        My file does not appear in my downloads, what should I do ?  </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      If you have a problem displaying your files in your download, make sure by consulting your invoices (User panel -> Billing information) that your files have been taken into account during the payment process. If your files are present on your invoice but they do not appear in your downloads, please contact us by email at : help (at) excelsportdata.com, we will help you find your files. 
                      </div>
                    </div>
                  </div>

                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        What if my file does not show any data ?  </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      If your file does not display any data, please contact us as soon as possible by email at : help (at) excelsportdata.com, please attach the faulty file to your email, we will do our best to help you find your data as quickly as possible.                      </div>
                    </div>
                  </div>


                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        Can files be refunded?  </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      Unfortunately no, due to the exportable nature of our products, we are unable to issue refunds. You can view a free sample of each file with a comment tailored to each product so you can make your purchasing decision with all the information you need.
                      </div>
                    </div>
                  </div>


                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        How long does it take for my files to appear in my downloads ? </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      Once your payment has been made, immediately find your purchased files in your user account under  "My Download"
                      </div>
                    </div>
                  </div>

                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        Can I create more than one user account? </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      You can create several accounts by making sure to change the login email address, we inform you  that accounts unused for 12 consecutive months and containing no purchase are automatically deleted from our system.
                      </div>
                    </div>
                  </div>

                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        Is it possible to view and download my invoices? </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      Yes, You can view and download your proof of payment from the "Billing Information" tab of your customer area.
                      </div>
                    </div>
                  </div>

                  <div class="kt-portlet kt-portlet--collapse" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                      <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-title">
                        How do I permanently delete my account? </span>
                      </div>
                      <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                          <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-plus portlet_controler"></i></a>
                        </div>
                      </div>
                    </div>
                    <div class="kt-portlet__body" kt-hidden-height="0" style="display:none">
                      <div class="kt-portlet__content">
                      You can send us a request to delete your user account under "Account Access" . Your account will be deleted instantly and all of your information will be deleted from our system. Be sure to locally back up your purchased files before deleting your user account.
                      </div>
                    </div>
                  </div>




                </div>
              </div>
              <div class="kt-section faq-ask pt-5 pb-5">
                <div class = "kt-container kt-grid__item mt-5">
                  <div class="row">
                    <div class="col-md-3"></div>
                    <form class="col-md-6 kt-form" id="faqaskForm" method = "post" action = "<?=base_url()?>faq/ask">
                      <div class="kt-container">
                        <div class="form-group kt-align-center mb-5">
                          <span class="text-green ft-6">DIDN'T YOU FIND WHAT YOU WERE LOOKING FOR?</span></br>
                          <span class="text-green ft-6">ASK US A QUESTION</span>
                        </div>
                        <div class="form-group row">
                          <label class="form-control-label">* Name</label>
                          <input class="form-control" type="text" name="username">
                        </div>
                        <div class="form-group row">
                          <label class="form-control-label">* Subject</label>
                          <input class="form-control" type="text" name="subject">
                        </div>
                        <div class="form-group row">
                          <label class="form-control-label">* Email</label>
                          <input class="form-control" type="email" name="email">
                        </div>
                        <div class="form-group row">
                          <label class="form-control-label">* Message</label>
                          <textarea class="form-control" rows="3" name="message"></textarea>
                        </div>
                        <div class="form-group row mt-5 kt-form__actions">
                          <button type="submit" class="btn btn-lg btn-form">Send</button>
                        </div>
                      </div>
                    </form>
                    <div class = "col-md-3"></div>
                  </div>
                </div>
              </div>
              <!-- end:: Content -->
