kkimport json
import requests
import base64
import sys

if (len(sys.argv) != 4):
  sys.exit('Expected input arguments: <sql_file> <eversql_api_key> <api_url>')

with  open(sys.argv[1]) as fp:
    contents = fp.read()
    count = 1;
    found_new_recommendations = False
    for curr_query in contents.split(';'):
        if (len(curr_query) > 10):
            query_bytes = curr_query.encode("utf-8")
            base64_bytes = base64.b64encode(query_bytes)
            base64_query = base64_bytes.decode("utf-8")

            response = requests.post(sys.argv[3], data={'query': base64_query, 'schema_structure': '', 'db_type' : 'MySQL', 'db_version' : '5.7', 'api_key': sys.argv[2]})
            
            print("")
            print("----  Query #" + str(count) + "  ----")
            query_without_newlines = ' '.join(curr_query.splitlines())
            print(query_without_newlines[:100].strip() + " ...")
            output = json.loads(response.text)
            if response.status_code == 200 and len(response.text) > 0:
                print("Total optimization recommendations found: ", len(output['recommendations']))
                print("Optimal indexing recommendations found: ", len(output['index_recommendations']))
                print("")
                for recommendation in output['recommendations']:
                    print("Recommendation: " + recommendation['title']);
                    print("Description: " + recommendation['desc']);
                    print("Best practice: " + recommendation['best_practice_sample']);
                    print("Instead of: " + recommendation['bad_practice_sample']);
                    print("")

                # print("More information: https://www.eversql.com/sql-query-optimizer/?query_id={}".format(output['query_id']))

                if (len(output['recommendations']) > 0):
                    found_new_recommendations = True
            else:
                print("Error occurred optimizing query: ", output['error'])

            print("\n")
            count += 1

    if (found_new_recommendations):
        sys.exit('Optimization recommendations are pending ...')
