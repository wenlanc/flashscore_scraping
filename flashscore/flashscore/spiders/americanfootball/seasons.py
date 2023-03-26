from flashscore.spiders.americanfootball.americanfootballBase import AmericanFootballBaseSpider
from flashscore.spiders.baseSpider import BaseSpider
import scrapy
from utils import SHEET_NAMES, saveToExcel

class SeasonsSpider(AmericanFootballBaseSpider):
    task_name = "seasons"
    name = ".".join([AmericanFootballBaseSpider.sportName(), task_name])

    @classmethod
    def taskName(cls):
        return cls.task_name

    def __init__(self, *args, **kwargs):
        self.leagues = kwargs.get("task")
        print("Leagues testing....")
        print(self.leagues)

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
        saveToExcel(allSeasons, super().sportName(), self.taskName())
