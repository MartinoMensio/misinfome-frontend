import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';
import { Observable, throwError } from 'rxjs';
import { delay, map, repeatWhen, switchMap, takeWhile } from 'rxjs/operators';

export enum LoadStates {
  None, Loading, Loaded, Error
}

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  API_URL = environment.api_url;
  most_popular_entries: Array<string> = [];

  constructor(private httpClient: HttpClient) { }

  getHome() {
    return this.httpClient.get(`${this.API_URL}/frontend/v2/home/`);
  }

  getMostPopularEntries() {
    if (this.most_popular_entries.length) {
      return new Observable((observer) => {

        // observable execution
        observer.next(this.most_popular_entries)
        observer.complete()
      })
    } else {
      return this.httpClient.get(`${this.API_URL}/frontend/v2/home/most_popular_entries`).pipe(
        map((result: any) => {
          this.most_popular_entries = result;
          return result;
        }))
    }
  }

  createJobProfileAnalysis(screen_name: string, until_id: number | null) {
    if (until_id) {
      return this.httpClient.get(`${this.API_URL}/frontend/v2/profiles?screen_name=${screen_name}&until_id=${until_id}`);
    } else {
      return this.httpClient.get(`${this.API_URL}/frontend/v2/profiles?screen_name=${screen_name}`);
    }
  }

  analyseProfile(screen_name: string, until_id: number | null = null) {
    return this.keepWatchingJobStatus(this.createJobProfileAnalysis(screen_name, until_id));
  }

  getJobStatus(status_id: string) {
    console.log(`getting status ${status_id}`);
    return this.httpClient.get(`${this.API_URL}/jobs/status/${status_id}`).pipe(
      map((res) => {
        console.log(res);
        return res;
      })
    );
  }

  getTweet(tweet_id: string) {
    return this.httpClient.get(`${this.API_URL}/frontend/v2/tweets/${tweet_id}?wait=true`);
  }

  private keepWatchingJobStatus(o: Observable<Object>) {
    return o.pipe(
      // switch to a new observable
      switchMap((job_create_res: any) => {
        const job_id = job_create_res.job_id;
        console.log(job_id);
        // every 2 seconds
        // return timer(0, 30000)
        return this.getJobStatus(job_id).pipe(
          repeatWhen(notifications => notifications.pipe(
            delay(2000),
          )),
          // .pipe(
          // get the result of the job
          // switchMap(() => {
          //   console.log('inside switchMap');
          //   return this.getJobStatus(job_id);
          // }),
          // and keep propagating the values while it's not completed
          takeWhile((val: any) => {
            console.log('checking the status of the job');
            console.log(val.state);
            console.log(val);
            // turn the failure into an exception
            if (val.state === 'FAILURE') {
              // the subscriber will receive an error in the error subscriber
              // throwError will notify the subscribers
              throwError(val);
              throw val; // return false wasn't enough (strange EmptyErrorImpl)
              // and the interval will stop to run
            }
            return val.state !== 'SUCCESS';
          }, true) // the inclusive flag lets also the false condition to get emitted (completed)
        );
      })
    );
  }
}
