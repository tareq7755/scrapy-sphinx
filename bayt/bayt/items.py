# -*- coding: utf-8 -*-

# Define here the models for your scraped items
#
# See documentation in:
# http://doc.scrapy.org/en/latest/topics/items.html

import scrapy


class BaytItem(scrapy.Item):    
    jobId       = scrapy.Field()
    url         = scrapy.Field()
    role        = scrapy.Field()    
    industry    = scrapy.Field()
    title       = scrapy.Field()
    company     = scrapy.Field()
    
    
    
    
    
    
    
