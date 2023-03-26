from flashscore.spiders.tennis.tennisBase import TennisBaseSpider
from flashscore.spiders.baseSpider import BaseSpider
import scrapy
from utils import SHEET_NAMES, saveToExcel

class CountriesSpider(TennisBaseSpider):
    task_name = "countries"
    name = ".".join([TennisBaseSpider.sportName(), task_name])

    @classmethod
    def taskName(cls):
        return cls.task_name

    def start_requests(self):
        yield scrapy.Request(url=super().baseUrl(), callback=self.parse)

    def parse(self, response):
        countrySels = response.css("div#category-left-menu>div>div>a")
        allCountries = []
        for countrySel in countrySels:
            try:
                id = countrySel.attrib["id"].split("_")[1]
                link = countrySel.attrib["href"].split("/")[2]
                name = countrySel.css('::text').get()
                allCountries.append((id, name, link))
            except:
                pass

        saveToExcel(allCountries, super().sportName(), self.taskName())
