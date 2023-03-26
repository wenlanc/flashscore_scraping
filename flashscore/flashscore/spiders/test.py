import scrapy
import os
import time

class TestSpider(scrapy.Spider):
    name = 'test'
    allowed_domains = ['quotes.toscrape.com']
    limit = 2

    def __init__(self, *args, **kwargs):
        self.base_url = 'http://quotes.toscrape.com/'
        self.result_Path = "result"

        self.urls = []
        for i in range(1, 11):
            self.urls.append((i, self.base_url + f"page/{i}"))

        testDirectory = os.path.join(self.result_Path, self.name)
        if not os.path.exists(testDirectory):
            os.makedirs(testDirectory)

    def start_requests(self):
        cnt = 0
        for (id, url) in self.urls:
            outPath = os.path.join(self.result_Path, self.name, f"page-{id}.html")
            if os.path.isfile(outPath):
                print("Since the file {} exists, skipping...".format(outPath))
            else:
                cnt += 1
                yield scrapy.Request(url=url, callback=self.parse, cb_kwargs=dict(id=id, outPath=outPath))
                if (cnt == self.limit):
                    break

    def parse(self, response, id, outPath):
        with open(outPath, 'wb') as f:
            f.write(response.body)
        self.log(f'Saved file {outPath}')