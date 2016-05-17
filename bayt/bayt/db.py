import psycopg2

class Database:        
    __connection = None
    
    def getConnection(self):
        """ gets a postgres connection instance. """
        
        if self.__connection is None:
            self.__connection = psycopg2.connect(database="bayt", user="tareq", password="tareq123", host="127.0.0.1", port="5432")
        return self.__connection
    
    
    def insertJobData(self, data):
        """ Inserts job data in job table. """
        
        connection = self.getConnection()
        cursor     = connection.cursor()
        
        query = "INSERT INTO job (job_id, job_url, role, industry, company, title) VALUES (%s, %s, %s, %s, %s, %s)";
        cursor.execute(query, (data['jobId'], data['url'], data['role'], data['industry'], data['company'], data['title']))
        connection.commit()