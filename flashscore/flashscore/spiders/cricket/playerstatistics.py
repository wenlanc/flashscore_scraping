from flashscore.spiders.cricket.cricketBase import CricketBaseSpider
from flashscore.spiders.baseSpider import BaseSpider
import scrapy
from utils import SHEET_NAMES, saveToExcel

class PlayersSpider(CricketBaseSpider):
    task_name = "player-statistics"
    name = ".".join([CricketBaseSpider.sportName(), task_name])

    @classmethod
    def taskName(cls):
        return cls.task_name

    def __init__(self, *args, **kwargs):
        self.players = kwargs.get("task")

    def start_requests(self):
        for (_, _, _, _, link) in self.players:
            url = BaseSpider.baseUrl() + link
            yield scrapy.Request(url=url, callback=self.parse, cb_kwargs=dict(link=link))

    def parse(self, response, link):
        name = response.css("div.teamHeader div.teamHeader__name::text").get().strip()
        birthday = response.css("div.teamHeader div.teamHeader__info--player-birthdate>span::text").get()
        birthday = birthday.split("(")[-1].split(")")[0].strip()
        result = {
            "Name": name,
            "Birthday": birthday,
            "Link": link
        }
        saveToExcel(result, super().sportName(), self.taskName())

