from flashscore.spiders.basketball.basketballBase import BasketballBaseSpider
from flashscore.spiders.baseSpider import BaseSpider
import scrapy
from utils import SHEET_NAMES, saveToExcel

class SeasonsSpider(BasketballBaseSpider):
    task_name = "seasons"
    name = ".".join([BasketballBaseSpider.sportName(), task_name])

    @classmethod
    def taskName(cls):
        return cls.task_name

    def __init__(self, *args, **kwargs):
        self.leagues = kwargs.get("task")
        self.taskType = kwargs.get("taskType")
    def start_requests(self):
        for (leagueId, leagueLink, lastSeasonLink) in self.leagues:
            url = "/".join([BaseSpider.baseUrl(), leagueLink, "archive"])
            yield scrapy.Request(url=url, callback=self.parse, cb_kwargs=dict(leagueId=leagueId, lastSeasonLink=lastSeasonLink))

    def parse(self, response, leagueId, lastSeasonLink):
        leagueSeasons = response.css("div#tournament-page-archiv>div.profileTable__row--background")
        allSeasons = []
        for leagueSeason in leagueSeasons:
            seasonTable = leagueSeason.css("div.leagueTable__season")
            season = seasonTable.css("div.leagueTable__seasonName>a")
            link = season.attrib["href"]
            name = season.css("::text").get()
            allSeasons.append((name, link, leagueId))
            if (link == lastSeasonLink):
                break
        saveToExcel(allSeasons, super().sportName(), self.taskName(), self.taskType)

