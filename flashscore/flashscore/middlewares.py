# Define here the models for your spider middleware
#
# See documentation in:
# https://docs.scrapy.org/en/latest/topics/spider-middleware.html

from scrapy import signals
from scrapy.exceptions import NotConfigured
from scrapy.http import HtmlResponse

# useful for handling different item types with a single interface
from itemadapter import is_item, ItemAdapter

from importlib import import_module

from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
import time

class FlashscoreSpiderMiddleware:
    # Not all methods need to be defined. If a method is not defined,
    # scrapy acts as if the spider middleware does not modify the
    # passed objects.

    @classmethod
    def from_crawler(cls, crawler):
        # This method is used by Scrapy to create your spiders.
        s = cls()
        crawler.signals.connect(s.spider_opened, signal=signals.spider_opened)
        return s

    def process_spider_input(self, response, spider):
        # Called for each response that goes through the spider
        # middleware and into the spider.

        # Should return None or raise an exception.
        return None

    def process_spider_output(self, response, result, spider):
        # Called with the results returned from the Spider, after
        # it has processed the response.

        # Must return an iterable of Request, or item objects.
        for i in result:
            yield i

    def process_spider_exception(self, response, exception, spider):
        # Called when a spider or process_spider_input() method
        # (from other spider middleware) raises an exception.

        # Should return either None or an iterable of Request or item objects.
        pass

    def process_start_requests(self, start_requests, spider):
        # Called with the start requests of the spider, and works
        # similarly to the process_spider_output() method, except
        # that it doesn’t have a response associated.

        # Must return only requests (not items).
        for r in start_requests:
            yield r

    def spider_opened(self, spider):
        spider.logger.info('Spider opened: %s' % spider.name)


class FlashscoreDownloaderMiddleware:
    """Scrapy middleware handling the requests using selenium"""

    def __init__(self, driver_name, driver_executable_path, driver_arguments, browser_executable_path):
        """Initialize the selenium webdriver

        Parameters
        ----------
        driver_name: str
            The selenium ``WebDriver`` to use
        driver_executable_path: str
            The path of the executable binary of the driver
        driver_arguments: list
            A list of arguments to initialize the driver
        browser_executable_path: str
            The path of the executable binary of the browser
        """

        webdriver_base_path = f'selenium.webdriver.{driver_name}'

        driver_klass_module = import_module(f'{webdriver_base_path}.webdriver')
        self.driver_klass = getattr(driver_klass_module, 'WebDriver')

        driver_options_module = import_module(f'{webdriver_base_path}.options')
        driver_options_klass = getattr(driver_options_module, 'Options')

        driver_options = driver_options_klass()
        if browser_executable_path:
            driver_options.binary_location = browser_executable_path
        for argument in driver_arguments:
            driver_options.add_argument(argument)

        self.driver_kwargs = {
            'executable_path': driver_executable_path,
            f'{driver_name}_options': driver_options
        }

        self.driver = None

    def init_driver(self):
        print("Initializing selenium request Web Driver...")
        self.driver = self.driver_klass(**self.driver_kwargs)

    def destroy_driver(self):
        if not self.driver == None:
            print("Destroying selenium driver - {}".format(self.driver))
            self.driver.close()
            self.driver.quit()
            self.driver = None

    @classmethod
    def from_crawler(cls, crawler):
        """Initialize the middleware with the crawler settings"""

        driver_name = crawler.settings.get('SELENIUM_DRIVER_NAME')
        driver_executable_path = crawler.settings.get('SELENIUM_DRIVER_EXECUTABLE_PATH')
        browser_executable_path = crawler.settings.get('SELENIUM_BROWSER_EXECUTABLE_PATH')
        driver_arguments = crawler.settings.get('SELENIUM_DRIVER_ARGUMENTS')

        if not driver_name or not driver_executable_path:
            raise NotConfigured(
                'SELENIUM_DRIVER_NAME and SELENIUM_DRIVER_EXECUTABLE_PATH must be set'
            )

        middleware = cls(
            driver_name=driver_name,
            driver_executable_path=driver_executable_path,
            driver_arguments=driver_arguments,
            browser_executable_path=browser_executable_path,
        )

        crawler.signals.connect(middleware.spider_closed, signals.spider_closed)

        return middleware

    def process_request(self, request, spider):
        # Called for each request that goes through the downloader
        # middleware.

        # Must either:
        # - return None: continue processing this request
        # - or return a Response object
        # - or return a Request object
        # - or raise IgnoreRequest: process_exception() methods of
        #   installed downloader middleware will be called
        """Process a request using the selenium driver if applicable"""

        # if not isinstance(request, SeleniumRequest):
        #     return None

        if spider.task_name == "leagues":
            return None

        if self.driver == None:
            self.init_driver()

        self.driver.get(request.url)

        if spider.name == "football.statistics":
            if request.cb_kwargs['type'] == "summary":
                #WebDriverWait(self.driver, 10).until(lambda driver: driver.find_elements(By.CLASS_NAME , 'detailMS') or driver.find_elements(By.CLASS_NAME, 'nodata-block'))
                WebDriverWait(self.driver, 20).until(lambda driver: driver.find_elements(By.CSS_SELECTOR , 'div[class*=verticalSections]') )
            elif request.cb_kwargs['type'] == "statistics":
                #WebDriverWait(self.driver, 10).until(lambda driver: driver.find_elements(By.CLASS_NAME , 'statBox') or driver.find_elements(By.CLASS_NAME, 'nodata-block'))

                WebDriverWait(self.driver, 20).until(lambda driver: driver.find_elements(By.ID , 'detail'))
                try:
                    WebDriverWait(self.driver, 20).until(lambda driver: driver.find_elements(By.CSS_SELECTOR , 'div[class*=statRow]'))
                except:
                    statRows = self.driver.find_elements(By.CSS_SELECTOR , 'div[class*=statRow]')
                    if len(statRows) < 2:
                        time.sleep(5)
        if spider.name == "basketball.statistics":
            if request.cb_kwargs['type'] == "summary":
                WebDriverWait(self.driver, 10).until(lambda driver: driver.find_elements(By.ID , 'detail') or driver.find_elements(By.CLASS_NAME, 'nodata-block'))
            elif request.cb_kwargs['type'] == "statistics":
                WebDriverWait(self.driver, 10).until(lambda driver: driver.find_elements(By.ID , 'detail') or driver.find_elements(By.CLASS_NAME, 'nodata-block'))

        if spider.name == "american-football.statistics":
            if request.cb_kwargs['type'] == "summary":
                WebDriverWait(self.driver, 10).until(lambda driver: driver.find_elements(By.ID , 'detail') or driver.find_elements(By.CLASS_NAME, 'noData___2uzVocT'))
            elif request.cb_kwargs['type'] == "statistics":
                WebDriverWait(self.driver, 10).until(lambda driver: driver.find_elements(By.ID , 'detail') or driver.find_elements(By.CLASS_NAME, 'noData___2uzVocT'))

        if spider.name == "tennis.statistics":
            if request.cb_kwargs['type'] == "summary":
                WebDriverWait(self.driver, 10).until(lambda driver: driver.find_elements(By.ID , 'detail') or driver.find_elements(By.CLASS_NAME, 'nodata-block'))
            elif request.cb_kwargs['type'] == "statistics":
                WebDriverWait(self.driver, 10).until(lambda driver: driver.find_elements(By.ID , 'detail') or driver.find_elements(By.CLASS_NAME, 'nodata-block'))

        if spider.name == "baseball.statistics":
            if request.cb_kwargs['type'] == "summary":
                WebDriverWait(self.driver, 10).until(lambda driver: driver.find_elements(By.ID , 'detail') or driver.find_elements(By.CLASS_NAME, 'nodata-block'))
            elif request.cb_kwargs['type'] == "statistics":
                WebDriverWait(self.driver, 10).until(lambda driver: driver.find_elements(By.ID , 'detail') or driver.find_elements(By.CLASS_NAME, 'nodata-block'))

        if spider.name == "cricket.statistics":
            if request.cb_kwargs['type'] == "summary":
                WebDriverWait(self.driver, 10).until(lambda driver: driver.find_elements(By.ID , 'parts') or driver.find_elements(By.CLASS_NAME, 'nodata-block'))
            elif request.cb_kwargs['type'] == "playerstatistics":
                WebDriverWait(self.driver, 10).until(lambda driver: driver.find_elements(By.CLASS_NAME , 'player-statistics') or driver.find_elements(By.CLASS_NAME, 'nodata-block'))
            elif request.cb_kwargs['type'] == "fallofwickets":
                WebDriverWait(self.driver, 10).until(lambda driver: driver.find_elements(By.ID , 'tab-fall-of-wickets-0-wickets') or driver.find_elements(By.CLASS_NAME, 'nodata-block'))

        # for cookie_name, cookie_value in request.cookies.items():
        #     self.driver.add_cookie(
        #         {
        #             'name': cookie_name,
        #             'value': cookie_value
        #         }
        #     )

        # if request.wait_until:
        #     WebDriverWait(self.driver, request.wait_time).until(
        #         request.wait_until
        #     )

        # if request.screenshot:
        #     request.meta['screenshot'] = self.driver.get_screenshot_as_png()

        # if request.script:
        #     self.driver.execute_script(request.script)

        if spider.task_name == "matches" or spider.task_name == "matchesfromplayer":
            cnt = 0
            while True:
                moreclicks = self.driver.find_elements_by_class_name("event__more")
                if len(moreclicks) == 0:
                    break

                self.driver.execute_script("arguments[0].click();", moreclicks[0])
                cnt += 1
                print("clicking {}th event_more to load more matches......".format(cnt))
                time.sleep(2)
                if cnt > 20:
                    break

        if spider.task_name == "players":
            cnt = 0
            while True:
                moreclicks = self.driver.find_elements_by_css_selector("div.rankingTable__row--more>a")

                if len(moreclicks) == 0:
                    break

                self.driver.execute_script("arguments[0].click();", moreclicks[0])
                cnt += 1
                print("clicking {}th event_more to load more matches......".format(cnt))
                time.sleep(2)
                if cnt > 20:
                    break
        if spider.task_name == "countries":
            cnt = 0
            while True:
                moreclicks = self.driver.find_elements_by_css_selector("span[class*='itemMore']")

                if len(moreclicks) == 0:
                    break

                self.driver.execute_script("arguments[0].click();", moreclicks[0])
                cnt += 1
                print("clicking {}th event_more to load more matches......".format(cnt))
                time.sleep(2)
                if cnt > 20:
                    break

        body = str.encode(self.driver.page_source)

        # Expose the driver via the "meta" attribute
        request.meta.update({'driver': self.driver})

        current_url = self.driver.current_url

        return HtmlResponse(
            current_url,
            body=body,
            encoding='utf-8',
            request=request
        )

    def process_response(self, request, response, spider):
        # Called with the response returned from the downloader.

        # Must either;
        # - return a Response object
        # - return a Request object
        # - or raise IgnoreRequest
        return response

    def process_exception(self, request, exception, spider):
        # Called when a download handler or a process_request()
        # (from other downloader middleware) raises an exception.

        # Must either:
        # - return None: continue processing this exception
        # - return a Response object: stops process_exception() chain
        # - return a Request object: stops process_exception() chain
        pass

    def spider_opened(self, spider):
        spider.logger.info('Spider opened: %s' % spider.name)

    def spider_closed(self, spider):
        """Shutdown the driver when spider is closed"""
        self.destroy_driver()
        spider.logger.info('Spider closed: %s' % spider.name)
