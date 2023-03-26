from flashscore.spiders.tennis.tennisBase import TennisBaseSpider
from flashscore.spiders.baseSpider import BaseSpider
import scrapy
from utils import SHEET_NAMES, saveToExcel

class LeaguesSpider(TennisBaseSpider):
    task_name = "leagues"
    name = ".".join([TennisBaseSpider.sportName(), task_name])

    @classmethod
    def taskName(cls):
        return cls.task_name

    def __init__(self, *args, **kwargs):
        self.tournamentIdSpecifier = "MC÷"
        self.tournamentLinkSpecifier = "ML÷"
        self.leagueNameSpecifier = "MN÷"
        self.leagueLinkSpecifier = "MU÷"
        self.leagueIdSpecifier = "MT÷"
        self.spliter = "~"
        self.subSpliter = "¬"

        self.tournaments = kwargs.get("task")

    def start_requests(self):
        for (id, name) in self.tournaments:
            url = "{}/m_{}_{}".format(BaseSpider.leagueUrl(), super().getSportId(), id)
            print(url)
            yield scrapy.Request(url=url, callback=self.parse, cb_kwargs=dict(tournamentName=name))

    def parse(self, response, tournamentName):
        result = response.text

        tmpList = result.split(self.spliter)
        if len(tmpList)<2:          # seems the tournament that have no this sport
            print("The country-{} don't have leagues on {}...".format(tournamentName, super().sportName()))
            return

        items = tmpList[0].split(self.subSpliter)
        tournamentId = items[0].split(self.tournamentIdSpecifier)[1]
        tournamentLink = items[1].split(self.tournamentLinkSpecifier)[1]

        allLeagues = []
        for tmp in tmpList[1:-1]:
            items = tmp.split(self.subSpliter)
            leagueName = items[0].split(self.leagueNameSpecifier)[1]
            leagueLink = items[1].split(self.leagueLinkSpecifier)[1]
            leagueId = items[2].split(self.leagueIdSpecifier)[1].split("_")[-1]
            allLeagues.append((leagueId, tournamentId, leagueName, leagueLink))

        saveToExcel(allLeagues, super().sportName(), self.taskName())
