from flashscore.spiders.football.footballBase import FootballBaseSpider
from flashscore.spiders.baseSpider import BaseSpider
import scrapy
from utils import SHEET_NAMES, saveToExcel, makeStatisticsKey, gloryTrim

class StatisticsSpider(FootballBaseSpider):
    task_name = "statistics"
    name = ".".join([FootballBaseSpider.sportName(), task_name])

    @classmethod
    def taskName(cls):
        return cls.task_name

    def __init__(self, *args, **kwargs):
        self.matches = kwargs.get("task")
        self.taskType = kwargs.get("taskType")
        self.base_url = BaseSpider.baseUrl() + '/match/'
        self.summary = "/#match-summary"
        self.statistics = "/#match-summary/match-statistics"

    def start_requests(self):
        for matchId in self.matches:
            yield scrapy.Request(url=self.base_url + matchId + self.summary, callback=self.parseSummary, cb_kwargs=dict(type="summary", matchId=matchId))

    def parseSummary(self, response, type, matchId):

        result = {}
        result["MATCH_ID"] = matchId
        descriptionSel = response.css("div[class*=tournamentHeaderDescription]>div[class*=tournamentHeader]")
        if len(descriptionSel) == 1:
            countryStr = gloryTrim(descriptionSel.css("span[class*=country]::text").get())
            result["COUNTRY"] = countryStr.strip()[:-1]
            description = gloryTrim(descriptionSel.css("span[class*=country]>a::text").get())
            leagueRound = description.split("-")
            result["COMPETITION"] = leagueRound[0].strip()
            if len(leagueRound) > 1:
                result["COMPETITION_ROUND"] = "-".join(leagueRound[1:]).strip()

        elif(len(descriptionSel) > 1):
            descriptionSel1 = descriptionSel[0]
            countryStr = gloryTrim(descriptionSel1.css("span[class*=country]::text").get())
            result["COUNTRY"] = countryStr.strip()[:-1]
            description = gloryTrim(descriptionSel1.css("span[class*=country]>a::text").get())
            leagueRound = description.split("-")
            result["COMPETITION"] = leagueRound[0].strip()
            if len(leagueRound) > 1:
                result["COMPETITION_ROUND"] = "-".join(leagueRound[1:]).strip()

        primarySels = response.css("div[class=duelParticipant]")
        if len(primarySels) > 0:
            primarySel = primarySels[0]
            if len(primarySels) > 1:
                primarySel = primarySels[1]

            dateTime = gloryTrim(primarySel.css("div[class*=startTime]>div::text").get())
            result["MATCH_DATE"] = dateTime.split(" ")[0]
            result["MATCH_TIME"] = dateTime.split(" ")[-1]

            result["HOME_TEAM_NAME"] = gloryTrim(primarySel.css("div[class*=home]>div[class*=participantNameWrapper]>div[class*=participantName]>a::text").get())
            result["AWAY_TEAM_NAME"] = gloryTrim(primarySel.css("div[class*=away] >div[class*=participantNameWrapper]>div[class*=participantName]>a::text").get())

            scoreTexts = primarySel.css("div[class*=score]>div[class*=matchInfo]>div[class*=wrapper]>span::text").getall()
            hG = aG = ""
            if len(scoreTexts)>0:
                hG = gloryTrim(scoreTexts[0])
            if len(scoreTexts)>1:
                aG = gloryTrim(scoreTexts[2])

            result["FULL_TIME_HOME_SCORE"] = hG
            result["FULL_TIME_AWAY_SCORE"] = aG

            result["MATCH_STATUS"] = gloryTrim(primarySel.css("div[class*=score]>div[class*=matchInfo]>div[class*=status]>span::text").get())

        detailSels = response.css("div[class*=verticalSections]")
        staLimits = 0
        if len(detailSels) > 0:
            detailSel = detailSels[0]
            scoreCells = detailSel.css("div[class*=incidentsHeader]")

            for scoreCell in scoreCells:
                staLimits += 1
                cells = scoreCell.css("div")
                quarter = gloryTrim(cells[0].css("::text").get())
                scores = cells[-1].css("span::text").getall()
                hG = aG = ""
                if len(scores)>0:
                    hG = gloryTrim(scores[0])
                if len(scores)>1:
                    aG = gloryTrim(scores[1])
                try:
                    result[makeStatisticsKey(super().sportName(), staLimits, "HOME", "score")] = hG
                    result[makeStatisticsKey(super().sportName(), staLimits, "AWAY", "score")] = aG
                except:
                    pass
            infoTexts = response.css("div[class*=data]>span::text").getall()
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

            result["HOME_GOAL_MINUTES"] = []
            result["AWAY_GOAL_MINUTES"] = []
            incidentRows = detailSel.css("div[class*=participantRow]")
            for row in incidentRows:
                if len(row.css("svg[class*=footballGoal]")) == 1:
                    goalTime = gloryTrim(row.css("div[class*=timeBox]::text").get())
                    if "home" in row.attrib["class"]:
                        result["HOME_GOAL_MINUTES"].append(goalTime)
                    if "away" in row.attrib["class"]:
                        result["AWAY_GOAL_MINUTES"].append(goalTime)

            result["HOME_GOAL_MINUTES"] = ",".join(result["HOME_GOAL_MINUTES"])
            result["AWAY_GOAL_MINUTES"] = ",".join(result["AWAY_GOAL_MINUTES"])
        yield scrapy.Request(url=self.base_url + matchId + self.statistics + "/1", callback=self.parseStatistics, cb_kwargs=dict(type="statistics", matchId=matchId, result=result, idx = 1))

    def parseStatistics(self, response, type, matchId, result, idx = 0):
        rowSels = response.css("div[id=detail]>div[class*=section]>div[class*=statRow]")
        if len(rowSels) > 0:
            if (idx > 0):
                for rowSel in rowSels:
                    stateCategory = rowSel.css("div[class*=statCategory]")
                    homeValue = gloryTrim(stateCategory.css("div[class*=statHomeValue]::text").get())
                    key = gloryTrim(stateCategory.css("div[class*=statCategoryName]::text").get())
                    awayValue = gloryTrim(stateCategory.css("div[class*=statAwayValue]::text").get())

                    hKey = makeStatisticsKey(super().sportName(), idx, "HOME", key)
                    aKey = makeStatisticsKey(super().sportName(), idx, "AWAY", key)

                    result[hKey] = homeValue
                    result[aKey] = awayValue
            idx = int(idx)        
            idx += 1
            yield scrapy.Request(url=self.base_url + matchId + self.statistics + "/" + str(idx), callback=self.parseStatistics, cb_kwargs=dict(type="statistics", matchId=matchId, result=result, idx = idx))
        else:
            print("Saving with half_" + str(idx))
            print(result)
            saveToExcel(result, super().sportName(), self.taskName(), self.taskType)
