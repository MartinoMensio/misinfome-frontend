import { ComponentFixture, TestBed } from '@angular/core/testing';

import { TweetResultComponent } from './tweet-result.component';

describe('TweetResultComponent', () => {
  let component: TweetResultComponent;
  let fixture: ComponentFixture<TweetResultComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ TweetResultComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(TweetResultComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
