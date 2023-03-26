from flashscore.spiders.baseball.baseballBase import BaseballBaseSpider
from flashscore.spiders.baseSpider import BaseSpider
import scrapy
from utils import SHEET_NAMES, saveToExcel

class PlayersSpider(BaseballBaseSpider):
    task_name = "players"
    name = ".".join([BaseballBaseSpider.sportName(), task_name])

    @classmethod
    def taskName(cls):
        return cls.task_name

    def __init__(self, *args, **kwargs):
        self.tournaments = kwargs.get("task")

    def start_requests(self):
        for (id, tournament, tournamentLink) in self.tournaments:
            url = "/".join([super().baseUrl(), "rankings", tournamentLink.split("/")[2].split("-")[0]])
            yield scrapy.Request(url=url, callback=self.parse, cb_kwargs=dict(count=0))

    def parse(self, response, count):
        playersSels = response.css("div.rankingTable__table>div.rankingTable__row")
        allPlayers = []
        idx = 0
        for playerSel in playersSels:
            idx += 1
            rank = playerSel.css("div.rank-column-rank::text").get().strip()[:-1]
            nameSel = playerSel.css("div.rank-column-player a")
            link = nameSel.attrib["href"]
            nationality = playerSel.css("div.rank-column-nationality div.rankingTable__nationality::text").get().strip()
            points = playerSel.css("div.rank-column-points::text").get().strip()
            tournaments = playerSel.css("div.rank-column-tournaments::text").get().strip()
            url = BaseSpider.baseUrl() + link
            yield scrapy.Request(url=url, callback=self.parsePlayer, cb_kwargs=dict(rank=rank, nationality=nationality, points=points, tournaments=tournaments, link=link))
            if (idx == count):
                # break
                pass
    
    def parsePlayer(self, response, rank, nationality, points, tournaments, link):
        name = response.css("div.teamHeader div.teamHeader__name::text").get().strip()
        birthday = response.css("div.teamHeader div.teamHeader__info--player-birthdate>span::text").get()
        birthday = birthday.split("(")[-1].split(")")[0].strip()
        result = {
            "Rank": rank,
            "Name": name,
            "Birthday": birthday,
            "Nationality": nationality,
            "Points": points,
            "Tournaments": tournaments,
            "Link": link
        }
        saveToExcel(result, super().sportName(), self.taskName())

