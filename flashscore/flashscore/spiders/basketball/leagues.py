from flashscore.spiders.basketball.basketballBase import BasketballBaseSpider
from flashscore.spiders.baseSpider import BaseSpider
import scrapy
from utils import SHEET_NAMES, saveToExcel

class LeaguesSpider(BasketballBaseSpider):
    task_name = "leagues"
    name = ".".join([BasketballBaseSpider.sportName(), task_name])

    @classmethod
    def taskName(cls):
        return cls.task_name

    def __init__(self, *args, **kwargs):
        self.countryIdSpecifier = "MC÷"
        self.countryLinkSpecifier = "ML÷"
        self.leagueNameSpecifier = "MN÷"
        self.leagueLinkSpecifier = "MU÷"
        self.leagueIdSpecifier = "MT÷"
        self.spliter = "~"
        self.subSpliter = "¬"

        self.countries = kwargs.get("task")

    def start_requests(self):
        for (id, name) in self.countries:
            url = "{}/m_{}_{}".format(BaseSpider.leagueUrl(), super().getSportId(), id)
            yield scrapy.Request(url=url, callback=self.parse, cb_kwargs=dict(countryName=name))

    def parse(self, response, countryName):
        result = response.text
        
        tmpList = result.split(self.spliter)
        if len(tmpList)<2:          # seems the country that have no this sport
            print("The country-{} don't have leagues on {}...".format(countryName, super().sportName()))
            return

        items = tmpList[0].split(self.subSpliter)
        countryId = items[0].split(self.countryIdSpecifier)[1]
        countryLink = items[1].split(self.countryLinkSpecifier)[1]

        allLeagues = []
        for tmp in tmpList[1:-1]:
            items = tmp.split(self.subSpliter)
            leagueName = items[0].split(self.leagueNameSpecifier)[1]
            leagueLink = items[1].split(self.leagueLinkSpecifier)[1]
            leagueId = items[2].split(self.leagueIdSpecifier)[1].split("_")[-1]
            allLeagues.append((leagueId, countryId, leagueName, leagueLink))

        saveToExcel(allLeagues, super().sportName(), self.taskName())




