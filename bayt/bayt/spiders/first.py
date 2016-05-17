from bayt.db import Database
from bayt.items import BaytItem
from scrapy.linkextractors import LinkExtractor
from scrapy.selector import Selector
from scrapy.spiders import CrawlSpider
from scrapy.spiders import Rule

class FirstSpider(CrawlSpider):
    name = "jobs"
    allowed_domains = ["bayt.com"]
    start_urls = ('http://www.bayt.com/en/jordan/', )

    rules = [
        Rule(
             LinkExtractor(allow=['/en/job/\w*']),
             callback='parse_item',
             follow=False
             )
    ]    
        
    def parse_item(self, response):
        """ parses the matching link item and extracts the data then inserts them in the `job` table"""        
        
        sel  = Selector(response)
        item = BaytItem()
        
        item['jobId']       = str(sel.xpath('//*[@id="main_content"]/div[6]/div[1]/dl[1]/dd/text()').extract()[0]).strip()
        item['url']         = response.url
        item['role']        = str(sel.xpath('//*[@id="main_content"]/div[6]/div[1]/dl[2]/dd[4]/text()').extract()[0]).strip()
        item['industry']    = str(sel.xpath('//*[@id="main_content"]/div[6]/div[1]/dl[2]/dd[2]/text()').extract()[0]).strip()
        item['title']       = str(sel.xpath('//*[@id="main_content"]/div[1]/div[1]/h1/text()').extract()[0]).strip()
        item['company']     = str(sel.xpath('//*[@id="main_content"]/div[1]/div[1]/h2/a[1]/text()').extract()[0]).strip()
        
        dbObject   = Database()
        dbObject.insertJobData(item)
        