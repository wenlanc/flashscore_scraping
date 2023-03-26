from flashscore.spiders.cricket.cricketBase import CricketBaseSpider
from flashscore.spiders.baseSpider import BaseSpider
import scrapy
from utils import SHEET_NAMES, saveToExcel, makeStatisticsKey, gloryTrim

class StatisticsSpider(CricketBaseSpider):
    task_name = "statistics"
    name = ".".join([CricketBaseSpider.sportName(), task_name])

    @classmethod
    def taskName(cls):
        return cls.task_name

    def __init__(self, *args, **kwargs):
        self.matches = kwargs.get("task")
        self.base_url = BaseSpider.baseUrl() + '/match/'
        self.summary = "/#match-summary"
        self.playerstatistics = "/#player-statistics"
        self.fallofwickets = "/#fall-of-wickets"

    def start_requests(self):
        for matchId in self.matches:
            yield scrapy.Request(url=self.base_url + matchId + self.summary, callback=self.parseSummary, cb_kwargs=dict(type="summary", matchId=matchId))

    def parseSummary(self, response, type, matchId):
        result = {}
        result["MATCH_ID"] = matchId
        descriptionSel = response.css("div[class*=description___]")
        if len(descriptionSel) == 1:
            countryStr = gloryTrim(descriptionSel.css("span.description__country::text").get())
            result["TOURNAMENT"] = countryStr.strip()[:-1]
            description = gloryTrim(descriptionSel.css("span.description__country>a::text").get())
            leagueRound = description.split("-")
            result["LEAGUE"] = leagueRound[0].strip()
            if len(leagueRound)>1:
                result["ROUND"] = "-".join(leagueRound[1:]).strip()
        if len(descriptionSel) > 1:
            descriptionSel0 = descriptionSel[0]
            countryStr = gloryTrim(descriptionSel0.css("span.description__country::text").get())
            result["TOURNAMENT"] = countryStr.strip()[:-1]
            description = gloryTrim(descriptionSel0.css("span.description__country>a::text").get())
            leagueRound = description.split("-")
            result["LEAGUE"] = leagueRound[0].strip()
            if len(leagueRound)>1:
                result["ROUND"] = "-".join(leagueRound[1:]).strip()

        primarySel = response.css("div.team-primary-content")
        if len(primarySel) == 1:
            result["HOME_TEAM_NAME"] = gloryTrim(primarySel.css("div.tname-home div.tname__text>a::text").get())
            result["AWAY_TEAM_NAME"] = gloryTrim(primarySel.css("div.tname-away div.tname__text>a::text").get())
            result["MATCH_STATUS"] = gloryTrim(primarySel.css("div.match-info>div.info-status::text").get())

        detailSel = response.css("table#parts")
        rowSels = detailSel.css("tbody tr")

        for rowSel in rowSels:
            colSels = rowSel.css("td")
            team = ""
            for scoreSel in colSels:
                if scoreSel.attrib["class"] == "server-home":
                    team = "HOME_TEAM"
                elif scoreSel.attrib["class"] == "server-away":
                    team = "AWAY_TEAM"
                elif scoreSel.attrib["class"] == "score":
                    score = gloryTrim(scoreSel.css("span::text").get())
                    score = score.split(" ")[0]
                    result["{}_SCORE".format(team)] = score
                elif scoreSel.attrib["class"] == "run-rate":
                    runrate = gloryTrim(scoreSel.css("span::text").get())
                    runrate = runrate.split("RR ")[-1]
                    result["{}_RUN_RATE".format(team)] = runrate

        infoSel = response.css("div.match-information-data")
        if len(infoSel) == 1:
            infoTexts = infoSel.css("div.content::text").getall()
            for infoText in infoTexts:
                if infoText.startswith("Attendance:"):
                    val = gloryTrim(infoText[12:])
                    if val[-1] == ",":
                        val = val[:-1]
                    result["ATTENDANCE"] = val
                elif infoText.startswith("Referee:"):
                    val = gloryTrim(infoText[9:])
                    if val[-1] == ",":
                        val = val[:-1]
                    result["REFEREE"] = val
                elif infoText.startswith("Venue:"):
                    val = gloryTrim(infoText[7:])
                    if val[-1] == ",":
                        val = val[:-1]
                    result["VENUE"] = val

        if result["MATCH_STATUS"] == "Abandoned":
            saveToExcel(result, super().sportName(), self.taskName())
        else:
            winner = result["MATCH_STATUS"].split(" won")[0]
            if "runs" in result["MATCH_STATUS"]:
                if winner == result["HOME_TEAM_NAME"]:
                    statkind = 0
                else:
                    statkind = 1
            else:
                if winner == result["HOME_TEAM_NAME"]:
                    statkind = 1
                else:
                    statkind = 0

            yield scrapy.Request(url=self.base_url + matchId + self.playerstatistics, callback=self.parsePlayerStatistics, cb_kwargs=dict(type="playerstatistics", matchId=matchId, statkind=statkind, result=result))

    def parsePlayerStatistics(self, response, type, matchId, statkind, result):
        statSel = response.css("div#player-statistics-content")
        if len(statSel) == 1:
            if statkind == 0:
                TEAMNAME = ["HOME_TEAM", "AWAY_TEAM"]
            else:
                TEAMNAME = ["AWAY_TEAM", "HOME_TEAM"]
            KINDS = ["", "BATS", "BOWL", "PARTNERSHIP"]
            KEYNAME = [
                [],
                ["", "BATSMAN", "STATUS", "RUNS", "BALLS", "MINUTES", "FOURS", "SIXES", "STRIKE_RATE"],
                ["", "BOWLER", "OVERS", "MAIDENS", "RUNS_CONCEDED", "WICKETS", "RUNS_CONCEDED_PER_OVER"],
                ["", "PARTNERSHIP", "RUNS", "MINUTES", "BALLS", "STRIKE_RATE"]
            ]

            for idx in range(0,2):
                statisticsSel = statSel.css("div#tab-player-statistics-{}-statistic".format(idx))
                if len(statisticsSel) == 1:
                    detailSels = statisticsSel.css("table.player-statistics")
                    kind = 0
                    for detailSel in detailSels:
                        kind += 1
                        if kind > 3:
                            break
                        rowSels = detailSel.css("tbody tr")
                        row = 0
                        for rowSel in rowSels:
                            try:
                                rowClass = rowSel.attrib["class"]
                            except:
                                rowClass = None
                            if rowClass in ["blank-line", None]:
                                continue
                            if "last" in rowClass:
                                colSels = rowSel.css("td")
                                col = 1
                                for colSel in colSels[1:]:
                                    col += 1
                                    value = gloryTrim("_".join(colSel.css("::text").getall()))
                                    if "(_" in value:
                                        value = value.split("(_")[-1].split(")")[0]
                                    result["{}_{}_EXTRAS_{}".format(TEAMNAME[idx], KINDS[kind], KEYNAME[kind][col])] = value
                            elif rowClass == "total-row":
                                colSels = rowSel.css("td")
                                col = 0
                                TOTALNAME = ["", "OVERS", "RUNS"]
                                for colSel in colSels[1:]:
                                    col += 1
                                    if col > 2:
                                        break
                                    value = gloryTrim(colSel.css("::text").get())
                                    if col == 1:
                                        value = value.strip()[1:-1].split(" ")[0]
                                    else:
                                        value = value.strip()
                                    result["{}_{}_TOTAL_{}".format(TEAMNAME[idx], KINDS[kind], TOTALNAME[col])] = value
                            else:
                                row += 1
                                colSels = rowSel.css("td")
                                col = 0
                                for colSel in colSels[1:]:
                                    col += 1
                                    value = gloryTrim("_".join(colSel.css("::text").getall()))
                                    result["{}_{}_{}_{}".format(TEAMNAME[idx], KINDS[kind], row, KEYNAME[kind][col])] = value

        yield scrapy.Request(url=self.base_url + matchId + self.fallofwickets, callback=self.parseFallofWickets, cb_kwargs=dict(type="fallofwickets", matchId=matchId, statkind=statkind, result=result))

    def parseFallofWickets(self, response, type, matchId, statkind, result):
        statSel = response.css("div#fall-of-wickets-content")

        if len(statSel) == 1:
            if statkind == 0:
                TEAMNAME = ["HOME_TEAM", "AWAY_TEAM"]
            else:
                TEAMNAME = ["AWAY_TEAM", "HOME_TEAM"]
            KEYNAME = ["", "BALL", "BATSMAN", "STATUS", "CURRENT_SCORE"]

            for idx in range(0,2):
                statisticsSel = statSel.css("div#tab-fall-of-wickets-{}-wickets".format(idx))
                if len(statisticsSel) == 1:
                    detailSel = statisticsSel.css("table.parts")
                    if len(detailSel) == 1:
                        rowSels = detailSel.css("tbody tr")
                        row = 0
                        for rowSel in rowSels:
                            row += 1
                            balls = gloryTrim(rowSel.css("td.ball::text").get()).split()[0].strip()
                            batsman = gloryTrim(rowSel.css("td.batsman::text").get())
                            statusLists = [e.strip() for e in rowSel.css("td.status ::text").getall()]
                            status = "_".join(statusLists)
                            currentScore = gloryTrim(rowSel.css("td.current-score::text").get())
                            result["{}_FALL_OF_WICKETS_{}_BALL".format(TEAMNAME[idx], row)] = balls
                            result["{}_FALL_OF_WICKETS_{}_BATSMAN".format(TEAMNAME[idx], row)] = batsman
                            result["{}_FALL_OF_WICKETS_{}_STATUS".format(TEAMNAME[idx], row)] = status
                            result["{}_FALL_OF_WICKETS_{}_CURRENT_SCORE".format(TEAMNAME[idx], row)] = currentScore

        saveToExcel(result, super().sportName(), self.taskName())
