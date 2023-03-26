from twisted.internet import reactor
from scrapy.utils.project import get_project_settings
from scrapy.crawler import CrawlerRunner
from scrapy.utils.log import configure_logging
import fire
import time
import utils
from database import DBManager

def sleep(_, duration=1):
    print(f'sleeping for: {duration} seconds between consequential crawl...')
    time.sleep(duration)  # block here

def loop_crawl(runner, spiderName, tasks=[], taskType=""):
    if tasks == None or len(tasks) == 0:
        return crawl(runner, spiderName, None)

    if len(tasks) == 1:
        return crawl(runner, spiderName, tasks[0], taskType)
    d = runner.crawl(spiderName, task=tasks[0], taskType=taskType)
    d.addBoth(sleep)
    d.addBoth(lambda _: loop_crawl(runner, spiderName, tasks[1:], taskType))
    return d

def crawl(runner, spiderName, task=None, taskType=""):
    d = runner.crawl(spiderName, task=task, taskType=taskType)
    d.addBoth(lambda _: reactor.stop())
    return d

def run(cmd="test"):
    sportName = ""
    taskName = ""
    taskType = ""
    try:
        cmd_splitted = cmd.split(".")
        if(len(cmd_splitted) == 2):
            sportName, taskName = cmd_splitted
        elif(len(cmd_splitted) == 3):
            sportName, taskName, taskType = cmd_splitted
        else:
            print("Invalid commandline: \"{}\", please check your command and try again...".format(cmd))
            return
    except:
        print("Invalid commandline: \"{}\", please check your command and try again...".format(cmd))
        return

    if sportName not in ["football", "tennis", "basketball", "baseball", "cricket", "american-football"]:
        print("Invalid sport name: \"{}\", please check your command and try again...".format(sportName))
        return

    runner = CrawlerRunner(get_project_settings())
    DBManager.init(host="localhost", user="flashscore", password="flashscore", db="flashscore")
    
    if taskName == "preprocess":
        utils.PreprocessingExcel(sportName)
        return
    elif taskName == "countries":
        crawl(runner, cmd)
    elif taskName in ["leagues", "seasons", "matches", "statistics", "players", "player-statistics", "matchesfromplayer"]:
        tasks = utils.loadTasks(sportName, taskName, taskType)
        if tasks == None:
            print("Can't read prio \"{}\" result, please try it first...".format(utils.PRIO_TASKS[taskName]))
            return
        numTasks = len(tasks)
        tasks = list(utils.divide_chunks(tasks, utils.BATCH_SIZE[taskName]))
        print("Starting {} with {} tasks, {} sub-tasks ...".format(cmd, numTasks, len(tasks)))
        loop_crawl(runner, sportName+"."+taskName, tasks, taskType)
    else:
        print("Invalid task name: \"{}\", please check your command and try again...".format(taskName))
        return

    reactor.run() # the script will block here until the crawling is finished

if __name__ == '__main__':
    configure_logging({'LOG_FORMAT': '%(levelname)s: %(message)s'})
    fire.Fire(run)
