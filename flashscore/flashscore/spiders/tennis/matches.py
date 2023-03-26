from flashscore.spiders.tennis.tennisBase import TennisBaseSpider
from flashscore.spiders.baseSpider import BaseSpider
import scrapy
from utils import SHEET_NAMES, saveToExcel, gloryTrim

class MatchesSpider(TennisBaseSpider):
    task_name = "matches"
    name = ".".join([TennisBaseSpider.sportName(), task_name])

    @classmethod
    def taskName(cls):
        return cls.task_name

    def __init__(self, *args, **kwargs):
        self.seasons = kwargs.get("task")

    def start_requests(self):
        for seasonLink in self.seasons:
            url = BaseSpider.baseUrl() + seasonLink + "results"
            yield scrapy.Request(url=url, callback=self.parse, cb_kwargs=dict(seasonLink=seasonLink))

    def parse(self, response, seasonLink):
        matchTable = response.css("div.sportName")
        allMatches = []
        if len(matchTable) == 1:
            rows = matchTable.css("div")
            leagueName = ""
            round = ""
            for row in rows:
                rowClass = row.attrib["class"]
                if "event__header" in rowClass:
                    leagueName = gloryTrim(row.css("span.event__title--name::text").get())
                elif "event__round" in rowClass:
                    round = gloryTrim(row.css("::text").get())
                elif "event__match" in rowClass and "event__match--static" in rowClass:
                    matchId = gloryTrim(row.attrib["id"]).split("_")[-1]
                    allMatches.append((matchId, seasonLink, leagueName, round))

        saveToExcel(allMatches, super().sportName(), self.taskName())
