from flashscore.spiders.tennis.tennisBase import TennisBaseSpider
from flashscore.spiders.baseSpider import BaseSpider
import scrapy
from utils import SHEET_NAMES, saveToExcel

class MatchesSpider(TennisBaseSpider):
    task_name = "matchesfromplayer"
    name = ".".join([TennisBaseSpider.sportName(), task_name])

    @classmethod
    def taskName(cls):
        return cls.task_name

    def __init__(self, *args, **kwargs):
        self.players = kwargs.get("task")

    def start_requests(self):
        for (_, _, _, _, playerLink) in self.players:
            url = BaseSpider.baseUrl() + playerLink + "/results"
            yield scrapy.Request(url=url, callback=self.parse)

    def parse(self, response):
        matchesTable = response.css("div.leagues--static.event--leagues.results")

        allMatches = []
        limit = 100
        cnt = 0
        if len(matchesTable) == 1:
            tournament = None
            rowElements = matchesTable.css("div.sportName.tennis>div")
            for rowElement in rowElements:
                classAttrib = rowElement.attrib["class"]
                if "event__header" in classAttrib:
                    tournament = rowElement.css("div.event__titleBox>span.event__title--type::text").get()
                    league = rowElement.css("div.event__titleBox>span.event__title--name::text").get()
                if ("ATP" not in tournament) and ("WTA" not in tournament):
                    continue
                if "event__match" in classAttrib:
                    try:
                        matchId = rowElement.attrib["id"].split("_")[-1]
                        allMatches.append((tournament, league, matchId))
                    except:
                        pass
                    cnt += 1
                    if cnt == limit:
                        break

        saveToExcel(allMatches, super().sportName(), self.taskName())


